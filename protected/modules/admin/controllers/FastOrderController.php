<?php

/**
 * 
 */
class FastOrderController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	/**
	 * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
	 */
	protected $defaultModelClass = 'FastOrder';

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
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','setStatus'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new FastOrder;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
        
		if(isset($_POST['FastOrder']))
		{
			$model->attributes=$_POST['FastOrder'];
			$model->status = 'active';
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['FastOrder']))
		{
			$model->attributes=$_POST['FastOrder'];
			if ( $_POST['FastOrder']['status'] == 'closed' )
			{// если заказ был обработан - запомним кто это сделал
			    $model->solverid = Yii::app()->getModule('user')->user()->id;
			}
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Отобразить все заказы
	 * 
	 * @todo перенести условия в defaultScopes в модели заказа
	 */
	public function actionIndex()
	{
	    // Определяем, какие заказы отображать
	    $display = Yii::app()->request->getParam('display');
	    
	    $criteria = $this->createCriteriaByDisplay($display);
	    
		$dataProvider=new CActiveDataProvider('FastOrder', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>30,
            ),
	    ));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new FastOrder('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['FastOrder']))
			$model->attributes=$_GET['FastOrder'];

		$this->render('admin',array(
			'model'=>$model,
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
		$model=FastOrder::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}*/

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='fast-order-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * 
	 * @param string $display
	 * @return CDbCriteria
	 */
	protected function createCriteriaByDisplay($display)
	{
	    $criteria = new CDbCriteria;
	    $criteria->order = '`timecreated` DESC';
	    
	    switch ( $display )
	    {
	        case 'active':
	            $criteria->addCondition("status='active'");
            break;
	        case 'pending':
	            $criteria->addCondition("status='pending'");
            break;
	        case 'closed':
	            $criteria->addCondition("status='closed'");
            break;
	        case 'my':
	            // мои заказы
	            $criteria->addCondition("status='pending'");
	            $criteria->addCondition("solverid=:solverid");
	            $criteria->params = array(':solverid' => Yii::app()->user->id);
            break;
	        default: $criteria->addCondition("status='active'");
	    }
	    
	    return $criteria;
	}
	
	/**
	 * Сменить статус заказа
	 * @param int $id - id заказа
	 * 
	 * @todo выводить разные сообщения в зависимости от статуса
	 * @todo сделать более подробное описание ошибок
	 */
	public function actionSetStatus($id)
	{
	    $model = $this->loadModel($id);
	    if ( ! $status = Yii::app()->request->getParam('status') )
	    {
	        throw new CHttpException(404,'Необходимо указать статус');
	    }
	    if ( $model->setStatus($status) )
	    {
	        Yii::app()->user->setFlash('success', 'Статус изменен');
	    }else
	   {
	       Yii::app()->user->setFlash('error', 'Не удалось изменить статус');
	    }
	    
	    $url = Yii::app()->createUrl('/admin/fastOrder');
	    $this->redirect($url);
	}
}
