<?php

/**
 * Контроллер главной страницы админки
 * 
 * @todo настроить права доступа через RBAC
 * @todo переместить функции крона в отдельный модуль, когда станет понятно как именно работать
 *       с cron-задачами в Yii
 */
class AdminController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     *      using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';
    
    /**
     * @return array
     *
     * @todo настроить проверку прав на основе RBAC
     */
    public function filters()
    {
        $baseFilters = parent::filters();
        $newFilters  = array(
            // фильтр для подключения YiiBooster 3.x (bootstrap 2.x)
            array(
                'ext.bootstrap.filters.BootstrapFilter + index, sendCustomerInvite, createNewGallery, mailPreview',
            ),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * @see CController::actions()
     */
    public function actions()
    {
        return array(
            // создать элемент оповещения
            'createListItem' => array(
                'class'      => 'application.actions.EcCreateAction',
                'modelName'  => 'EasyListItem',
            ),
            // редактировать элемент оповещения
            'updateListItem' => array(
                'class'      => 'application.actions.EcUpdateAction',
                'modelName'  => 'EasyListItem',
            ),
            // удалить элемент оповещения
            'deleteListItem' => array(
                'class'      => 'application.actions.EcDeleteAction',
                'modelName'  => 'EasyListItem',
            ),
        );
    }
    
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
	    Yii::app()->getModule('admin')->cron();
	}
	
	/**
	 * Отправляет часть накопившейся почты, учитывая ограничения хостинга Amazon
	 *
	 * @return null
	 * @deprecated
	 */
	public function actionSendMail()
	{
	    Yii::app()->getModule('admin')->cronTaskSendMail();
	}
	
	/**
	 * Загрузка картинок на сервер Amazon S3
	 * 
	 * @return null
	 * @deprecated
	 */
	public function actionUploadImages()
	{
	    Yii::app()->getModule('admin')->cronTaskUploadImages();
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
        
	    $invite = new CustomerInvite;
	    $invite->email      = $email;
	    $invite->objecttype = $objectType;
	    $invite->objectid   = $objectId;
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
	    $gallery->limit       = 40;
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
	 * Предварительный просмотр email-сообщений перед отправкой
	 * 
	 * @return void
	 * 
	 * @todo создавать временный объект для каждого оповещения (в зависимости от типа)
	 */
	public function actionMailPreview()
	{
	    // служебное название для оповещения
	    $action = Yii::app()->request->getParam('action');
	    
	    if ( $action === 'newInvite' )
	    {
	        $questionary = Yii::app()->getModule('user')->user()->questionary;
	        // для демонстрации письма нам понадобится приглашение
	        $invite = EventInvite::model()->forModel($questionary)->
                withStatus('pending')->find(array('limit' => 1));
	        if ( ! $invite )
	        {// создаем просматривающему админу приглашение на роль
	            $invite = new EventInvite();
    	        $invite->questionaryid = $questionary->id;
    	        $invite->eventid = 484;
    	        $invite->save();
	        }
	        $params['invite'] = $invite;
	    }
	    // выводим содержимое письма
	    $mailComposer = Yii::app()->getModule('mailComposer');
	    echo $mailComposer::getMessage($action, $params);
	}
	
	/**
	 * 
	 * @param CustomerInvite $invite
	 * @return array
	 */
	/*public function performInviteAjaxValidation($invite)
	{
	    if ( isset($_POST['ajax']) && $_POST['ajax']==='customer-invite-form' )
	    {
	        echo CActiveForm::validate($model);
	        Yii::app()->end();
	    }
	}*/
}