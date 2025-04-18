<?php

/**
 * Контроллер для работы с отсылкой коммерческих предложений
 */
class CustomerOfferController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';
	/**
	 * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
	 */
	protected $defaultModelClass = 'CustomerOffer';
	
	/**
	 * @see CController::init()
	 */
	public function init()
	{
	    //Yii::import('application.modules.projects.models.*');
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
	        'postOnly + delete',
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
	 */
	public function actionCreate()
	{
		$model = new CustomerOffer;
		
		// тип коммерческого предложения всегда одинаковый
		$objectType = 'offer';
		$objectId   = Yii::app()->request->getParam('objectId', 0);
		
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
		
		// устанавливаем значения по умолчанию
		$model->objecttype = $objectType;
		$model->objectid   = $objectId;
		$model->name       = '';

		if ( $attributes = Yii::app()->request->getPost('CustomerOffer') )
		{
			$model->attributes = $attributes;
			if ( $model->validate() AND $model->save() )
			{// @todo проставить setFlash здесь и на странице отображения
			    // При сохранении записи письмо отправляется автоматически
			    
			    $this->redirect(array('create'));
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

		if ( $attributes = Yii::app()->request->getPost('CustomerOffer') )
		{
			$model->attributes = $attributes;
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id' => $model->id));
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
		$model = new CustomerOffer('search');
		$model->unsetAttributes();  // clear any default values
		
		if ( isset($_GET['CustomerOffer']) )
		{
		    $model->attributes = $_GET['CustomerOffer'];
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
		$model = CustomerOffer::model()->findByPk($id);
		if ( $model === null )
		{
		    throw new CHttpException(404, 'Коммерческое предложение не найдено');
		}
		return $model;
	}*/

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'customer-offer-form' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}