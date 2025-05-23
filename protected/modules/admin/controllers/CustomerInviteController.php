<?php

/**
 * Контроллер для работы с приглашениями заказчиков
 */
class CustomerInviteController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column1';
	/**
	 * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
	 */
	protected $defaultModelClass = 'CustomerInvite';
	
	/**
	 * @see CController::init()
	 */
	public function init()
	{
	    Yii::import('application.modules.projects.models.*');
	    parent::init();
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		$baseFilters = parent::filters();
        $newFilters  = array(
            'accessControl',
            array(
                'ext.bootstrap.filters.BootstrapFilter',
            ),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 * 
	 * @todo прописать права доступа через RBAC
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('admin', 'index', 'view', 'create', 'update'),
				'users'   => array('@'),
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * 
	 * @todo сделать проверку допустимых значений objecttype
	 */
	public function actionCreate()
	{
		$model = new CustomerInvite;
		// AJAX validation
		$this->performAjaxValidation($model);
		
		$objectType      = Yii::app()->request->getParam('objectType');
		$objectId        = Yii::app()->request->getParam('objectId', 0);
		if ( ! $objectType )
		{// Определяем куда создается приглашение
		    throw new CHttpException(400, 'Не указан тип объекта для создания приглашения');
		}
		
		switch ( $objectType )
		{
		    case 'project': 
		        $objectExists = Project::model()->exists('id = :id', array(':id' => $objectId));
	        break;
		    case 'event':
		        $objectExists = ProjectEvent::model()->exists('id = :id', array(':id' => $objectId));
	        break;
		    case 'vacancy':
		        $objectExists = EventVacancy::model()->exists('id = :id', array(':id' => $objectId));
	        break;
		    default: $objectExists = false;
		}
		if ( ! $objectExists )
		{
		    throw new CHttpException(404, 'Не найден объект для создания приглашения');
		}
		// связываем приглашение с нужным объектом при создании
		$model->objecttype = $objectType;
		$model->objectid   = $objectId;

		if ( $attributes = Yii::app()->request->getPost('CustomerInvite') )
		{
			$model->attributes = $attributes;
			if ( $model->validate() AND $model->save() )
			{// получаем список статусов, которые должны присутствовать в приглашении
			    $statuses = Yii::app()->request->getParam('statuses');
			    if ( empty($statuses) )
			    {// эй, ну хоть что-то мы должны отправить?
			        throw new CHttpException(400, 'Нужно выбрать хотя бы одну галочку в списке статусов');
			    }
			    // включать или не включать контакты в письмо
			    $displayContacts = Yii::app()->request->getParam('displayContacts', 0);
			    
			    // сохраняем статусы и другие связанные данные, которые нужно отобразить заказчику
			    // @todo в связи с новым виджетом отбора людей возможно больше не актуально
			    $model->saveData(array(
			        'statuses'        => $statuses,
			        'displayContacts' => $displayContacts,
			    ));
			    // сообщаем о результате
			    Yii::app()->user->setFlash('success', 'Приглашение отправлено');
			    
			    // после создания приглашения перенаправляем пользователя на страницу создания 
			    $this->redirect(array('create', 
			        'objectType' => $objectType,
			        'objectId'   => $objectId,
			    ));
			}
		}
		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( $attributes = Yii::app()->request->getPost('CustomerInvite') )
		{
			$model->attributes = $attributes;
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id'=>$model->id));
			}
		}

		$this->render('update', array(
			'model' => $model,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->actionAdmin();
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
	    // Определяем куда создается приглашение
	    if ( ! $objectType = Yii::app()->request->getParam('objectType') )
	    {
	        throw new CHttpException(400, 'Не передан объект для которого создается приглашение');
	    }
	    $objectId = Yii::app()->request->getParam('objectId', 0);
		$model = new CustomerInvite('search');
		$model->unsetAttributes();  // clear any default values
		
		if ( isset($_GET['CustomerInvite']) )
		{
		    $model->attributes = $_GET['CustomerInvite'];
		}

		$this->render('admin', array(
			'model' => $model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * 
	 * @param integer the ID of the model to be loaded
	 */
	/*public function loadModel($id)
	{
		$model = CustomerInvite::model()->findByPk($id);
		if ( $model === null )
		{
		    throw new CHttpException(404, 'Приглашение не найдено');
		}
		return $model;
	}*/

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'customer-invite-form' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
