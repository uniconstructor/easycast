<?php

/**
 * Админская часть сайта
 * @todo пускать в админку по ключу (только на определенные страницы)
 * @todo вынести все функции cron в отдельный behavior-класс
 */
class AdminModule extends CWebModule
{
    /**
     * @var array
     */
    public $controllerMap = array(
        'fastOrder' => array(
            'class' => 'application.modules.admin.controllers.FastOrderController',
        ),
    );
    /**
     * @var array - массив id фрагментов кода (клипов), для всплывающих форм сложных значений
     *              В форме анкеты требуется вывести множество "дочерних форм", а вкладывать их
     *              друг в друга нельзя поэтому выбрано такое решение
     *              Подробнее см. документацию класса CClipWidget
     */
    public $formClips = array();
    
    /**
     * @see CModule::init()
     */
	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'admin.models.*',
			'admin.components.*',
		    // Подключаем модели проекта - чтобы можно было работать с событиями и проектами
		    'projects.models.*',
		    
		    // Подключаем загрузку изображений
		    'ext.galleryManager.*',
		    'ext.galleryManager.models.*',
		));
		
		$this->defaultController = 'admin';
	}
    
	/**
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
		if( parent::beforeControllerAction($controller, $action) )
		{
    		$user = Yii::app()->getUser();
            if( $user->isGuest === true )
            {// просим авторизоваться перед входом в админку
                $user->loginRequired();
            }
            if ( ! Yii::app()->user->checkAccess('Admin') )
            {// если нет прав доступа - делаем вид что админки здесь нет
                throw new CHttpException(404, 'Страница не найдена');
                return false;
            }
			return true;
		}else
		{
		    return false;
		}
	}
	
	/**
	 * @param $str
	 * @param $params
	 * @param $dic
	 * @return string
	 */
	public static function t($str='', $params=array(), $dic='admin')
	{
	    if ( Yii::t("AdminModule", $str) == $str )
	    {
	        return Yii::t("AdminModule.".$dic, $str, $params);
	    }else
	    {
	        return Yii::t("AdminModule", $str, $params);
	    }
	}
	
	/**
	 * Выполнить все cron-задачи модуля admin
	 * @param array $tasks - список задач, которые необходимо выполнить, ключ массива - название задачи
	 *                       значение - список параметров для выполнения
	 * @return void
	 */
	public function cron($tasks=null)
	{
	    if ( Yii::app()->params['useCron'] )
	    {
	        // отправка писем стоящих в очереди
	        $this->cronTaskSendMail();
	        // загрузка на S3 изображений, которые не получилось выгрузить с первого раза
	        $this->cronTaskUploadImages();
	    }
	    // очистка устаревших блокировок
	    ObjectLock::model()->clearLocks();
	}
	
	/**
	 * Загрузка картинок на сервер Amazon S3
	 * Обычно изображения загружаются на S3 в тот же момент когда пользователь загружает их на сайт
	 * Но иногда некоторые изображения не получается загрузить с первого раза и они остаются на веб-сервере
	 * Эта функция собирает все изображения, которые не удалось загрузить с первого раза и повторяет загрузку
	 * 
	 * @param int $limit - максимальное количество изображений, которое будет загружено за один раз
	 *                      
	 * @return null
	 */
	public function cronTaskUploadImages($limit=3)
	{
	    // служебные задачи прерывать нельзя
	    ignore_user_abort(true);
	    set_time_limit(0);
	    
	    // подключаем нужные модели
	    Yii::import('application.extensions.galleryManager.models.*');
	    Yii::import('application.extensions.galleryManager.components.*');
	    Yii::import('application.extensions.galleryManager.*');
	
	    // выбираем незагруженные фотографии
	    // @todo для избежания конфликта - смотреть только на фотографии которые лежат незагруженными
	    //       дольше 15 минут
	    $criteria = new CDbCriteria;
	    $criteria->condition = '(`timemodified` > `timeuploaded`) OR (`timeuploaded` = 0)';
	    $criteria->order = '`timemodified` DESC';
	    $criteria->limit = 3;
	    $photos = GalleryPhoto::model()->findAll($criteria);
	     
	    foreach ( $photos as $photo )
	    {
	        ob_start ();
	        echo 'Uploading photo '.$photo->id."<br>";
	        ob_flush();
	        try
	        {// trying to upload the photo
	            GmS3Photo::setImageS3($photo);
	        } catch ( Exception $e )
	        {
	            echo 'Timeout. Another try...'."<br>";
	            // need to comment this part.
	            // Sometimes, amazon server suddenly close socket connection by timeout.
	            // It happens in a very few cases by we shoud keep it in mind.
	            // In this case we just restart the upload process
	            try
	            {
	                GmS3Photo::setImageS3($photo);
	                echo 'Success.';
	            }catch ( Exception $e )
	            {// second error on same file shoud never happen
	                // cron will take care about skipped photos later anyway
	                echo 'Failed. Move to next photo. Image Skipped. '."<br>";
	            }
	        }
	
	        unset($photo);
	        ob_end_flush();
	    }
	    echo 'Все изображения загружены. Последняя синхронизация '.date('Y-m-d H:i:s', time());
	
	    // Считаем сколько осталось загрузить
	    $totalCount = GalleryPhoto::model()->count($criteria);
	    echo '<br>Осталось загрузить '.$totalCount;
	}
	
	/**
	 * Отправляет часть накопившейся почты, учитывая ограничения хостинга Amazon
	 * @param int $count - сколько раз вызвать рассылку (за один раз из очереди отправляется несколько писем)
	 * @return null
	 */
	public function cronTaskSendMail($count=4)
	{
	    // служебные задачи прерывать нельзя
	    ignore_user_abort(true);
	    set_time_limit(0);
	    
	    $ecawsapi = Yii::app()->getComponent('ecawsapi');
	    $ecawsapi->trace = true;
	
	    echo '<pre>';
	    echo "Sending email...\n";
	    if ( $ecawsapi->emailQueueIsEmpty() )
	    {// очередь сообщений пуста - ничего не нужно отправлять
	        echo "Queue empty.\n";
	        return 0;
	    }
	    for ( $i = 0; $i < 4; $i++ )
	    {// отправляем по 20 писем за 1 запуск крона
	        $ecawsapi->processEmailQueue();
	        if ( $ecawsapi->emailQueueIsEmpty() )
	        {// все сообщения отправлены
	            break;
	        }
	    }
	    // в конце выводим статистику, сколько осталось
	    $ecawsapi->showEmailQueryInfo();
	
	    echo "Done.\n\n";
	    echo '</pre>';
	    return 0;
	}
}
