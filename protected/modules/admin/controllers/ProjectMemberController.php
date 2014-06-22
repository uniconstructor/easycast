<?php

class ProjectMemberController extends Controller
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
		$baseFilters = parent::filters();
	    $newFilters  = array(
	        'accessControl',
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
		$model=new ProjectMember;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ProjectMember']))
		{
			$model->attributes=$_POST['ProjectMember'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
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

		if(isset($_POST['ProjectMember']))
		{
			$model->attributes=$_POST['ProjectMember'];
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
	    $this->layout='//layouts/column1';
	    $type      = Yii::app()->request->getParam('type');
	    
	    $projectid = Yii::app()->request->getParam('projectid');
	    $eventid   = Yii::app()->request->getParam('eventid');
	    $vacancyid = Yii::app()->request->getParam('vacancyid');
	    $vacancyid = Yii::app()->request->getParam('vid', $vacancyid);
	    
	    
		$this->render('index',array(
			'projectid' => $projectid,
			'eventid'   => $eventid,
			'vacancyid' => $vacancyid,
			'type'      => $type,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ProjectMember('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ProjectMember']))
			$model->attributes=$_GET['ProjectMember'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ProjectMember::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-member-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Изменяет статус заявки участника (подтверждает или отклоняет)
	 * @param int $id - id заявки в таблице {{project_members}}
	 * @throws CHttpException
	 * @return null
	 * 
	 * @todo убрать весь старый код и обращаться к контроллеру projects/MemberController
	 */
    public function actionSetStatus($id)
	{
	    $model = $this->loadModel($id);
	    if ( ! $status = Yii::app()->request->getParam('status') )
	    {
	        throw new CHttpException(404,'Необходимо указать статус');
	    }
	    $model->setStatus($status);
	    
	    if ( $status == ProjectMember::STATUS_ACTIVE )
	    {
	        Yii::app()->user->setFlash('success', 'Заявка подтверждена');
	    }elseif ( $status == ProjectMember::STATUS_REJECTED )
	    {
	        Yii::app()->user->setFlash('info', 'Заявка отклонена');
	    }
	
	    $url = Yii::app()->createUrl('/admin/projectMember/index/', array(
	        'projectid' => $model->vacancy->event->project->id,
	        'type' => 'applications',
	    ));
	    $this->redirect($url);
	}
}
