<?php

class ProjectEventController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
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
		$model = new ProjectEvent;
		
		$projectid = Yii::app()->request->getParam('projectid', 0);
		$groupid   = Yii::app()->request->getParam('parentid', 0);
		$type      = Yii::app()->request->getParam('type', 'event');
		if ( ! $project = Project::model()->findByPk($projectid) )
		{
		    throw new CHttpException(404,'Необходимо указать id проекта');
		}
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset($_POST['ProjectEvent']) )
		{
		    $_POST['ProjectEvent']['projectid'] = $projectid;
			$model->attributes = $_POST['ProjectEvent'];
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id'=>$model->id));
			}
		}

		$this->render('create', array(
			'model'   => $model,
		    'project' => $project,
		    'type'    => $type,
		    'groupid' => $groupid,
		));
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

		if(isset($_POST['ProjectEvent']))
		{
			$model->attributes=$_POST['ProjectEvent'];
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
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('ProjectEvent');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		/*$model=new ProjectEvent('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ProjectEvent']))
			$model->attributes=$_GET['ProjectEvent'];

		$this->render('admin',array(
			'model'=>$model,
		));*/
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ProjectEvent::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-event-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
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
	    
	
	    $url = Yii::app()->createUrl('/admin/projectEvent/view', array('id' => $id));
	    $this->redirect($url);
	}
}
