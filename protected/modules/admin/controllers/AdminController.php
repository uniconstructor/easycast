<?php

/**
 * Контроллер главной страницы админки
 * @todo настроить права доступа
 */
class AdminController extends Controller
{
    public $layout='//layouts/column2';
    
    /**
     * Отображение главного меню администратора
     */
	public function actionIndex()
	{
		$this->render('index');
	}
	
	/**
	 * @todo Заготовка для крона
	 * 
	 * @return null
	 */
	public function actionCron()
	{
	    ignore_user_abort(true);
        set_time_limit(0);
        
        // загрузка картинок на сервер S3
        $this->actionUploadImages();
	    // рассылка почты
	    $this->actionSendMail();
	}
	
	/**
	 * Отправляет часть накопившейся почты, учитывая ограничения хостинга
	 *
	 * @return null
	 */
	public function actionSendMail()
	{
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
	
	/**
	 * загрузка картинок на сервер S3
	 * 
	 * @return null
	 */
	public function actionUploadImages()
	{
	    // подключаем нужные модели
	    Yii::import('application.extensions.galleryManager.models.*');
	    Yii::import('application.extensions.galleryManager.components.*');
	    Yii::import('application.extensions.galleryManager.*');
	     
	    // выбираем не загруженные фотографии
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
	 * @deprecated 
	 */
	public function actionShareAccess()
	{
	    echo 'OK';
	}
	
	/**
	 * Отправить заказчику(режиссеру) одноразовую ссылку для доступа к отбору актеров
	 *
	 * @return null
	 */
	public function actionSendCustomerInvite()
	{
	    $model = new CustomerInvite;
	    
	    // Uncomment the following line if AJAX validation is needed
	    $this->performInviteAjaxValidation($model);
	    
	    if ( $attributes = Yii::app()->request->getPost('CustomerInvite') )
	    {
	        $model->attributes = Yii::app()->request->getPost('CustomerInvite');
	        if ( $model->save() )
	        {
	            $this->redirect(array('view', 'id' => $model->id));
	        }
	    }
	    
	    $this->render('create',array(
	        'model' => $model,
	    ));
	    /*if ( ! $email = Yii::app()->request->getParam('email', '') )
	    {
	        throw new CHttpException(400, 'Не указан email');
	    }
	    if ( ! $objectType = Yii::app()->request->getParam('objectType', '') )
	    {
	        throw new CHttpException(400, 'Не указан objectType');
	    }
	    if ( ! $objectId = Yii::app()->request->getParam('objectId', 0) )
	    {
	        throw new CHttpException(400, 'Не указан objectId');
	    }*/
        
	    $invite = new CustomerInvite;
	    $invite->email      = $email;
	    $invite->objecttype = $objectType;
	    $invite->objectid   = $objectId;
	    //$invite->name
	}
	
	/**
	 * Создать новую, ни к чему не привязанную пустую галерею и
	 * Получить виджет GalleryManager для загрузки в нее изображений
	 * Используется в тех случаях когда нам нужно загрузить фотографии раньше чем создать объект,
	 * или в тех случаях когда галерея не приявязана к объекту CActiveRecord
	 * (например как в случае с фотографиями вручную добавленных в фотовызывной участников)
	 * 
	 * @return void
	 */
	public function actionCreateNewGallery()
	{
	    Yii::import('ext.galleryManager.*');
	    Yii::import('ext.galleryManager.models.*');
	    
	    $gallery = new PhotoGallery();
	    $gallery->versions    = Yii::app()->getModule('questionary')->gallerySettings['versions'];
	    $gallery->limit       = 1;
	    $gallery->name        = 1;
	    $gallery->description = 1;
	    $gallery->save(false);
	    
	    // в качестве ответа на AJAX-запрос
	    $this->widget('GalleryManager', array(
	        'gallery'         => $gallery,
	        'controllerRoute' => '/admin/gallery'
	    ));
	}
	
	/**
	 * 
	 * @param CustomerInvite $invite
	 * @return array
	 */
	public function performInviteAjaxValidation($invite)
	{
	    if ( isset($_POST['ajax']) && $_POST['ajax']==='customer-invite-form' )
	    {
	        echo CActiveForm::validate($model);
	        Yii::app()->end();
	    }
	}
}