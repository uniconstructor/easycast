<?php

/**
 * Контроллер главной страницы админки
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
}