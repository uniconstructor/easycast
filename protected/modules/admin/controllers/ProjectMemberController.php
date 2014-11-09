<?php

/**
 * Контроллер для работы с заявками участников в админке
 */
class ProjectMemberController extends Controller
{
	/**
	 * @var string - the default layout for the views. Defaults to '//layouts/column2', meaning
	 *               using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

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
	 * @see CController::actions()
	 */
	public function actions()
	{
	    return array(
	        'upload' => array(
	            'class'      => 'xupload.actions.S3XUploadAction',
	            'objectType' => 'ProjectMember',
	            //'path'       => "s3://video.easycast.ru/uploads/",
	            //'publicPath' => "https://s3.amazonaws.com/video.easycast.ru/",
	        ),
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
			array('allow',
				'actions' => array('index', 'view'),
				'users'   => array('@'),
			),
			array('allow',
				'actions' => array('create', 'update', 'setStatus'),
				'users'   => array('@'),
			),
			array('allow',
				'actions' => array('admin', 'delete', 'changeVacancy', 'upload', 'uploadPage'),
			    'roles'   => array('admin'),
			),
			array('deny',
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
		$model = new ProjectMember;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if ( isset($_POST['ProjectMember']) )
		{
			$model->attributes = $_POST['ProjectMember'];
			if($model->save())
				$this->redirect(array('view', 'id'=>$model->id));
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

		if ( isset($_POST['ProjectMember']) )
		{
			$model->attributes = $_POST['ProjectMember'];
			if( $model->save() )
			{
			    $this->redirect(array('view', 'id' => $model->id));
			}
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
			if( ! isset($_GET['ajax']) )
			{
			    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		}else
		{
		    throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
	    $this->layout = '//layouts/column1';
	    
	    $type      = Yii::app()->request->getParam('type');
	    $projectid = Yii::app()->request->getParam('projectid');
	    $eventid   = Yii::app()->request->getParam('eventid');
	    
	    if ( ! $vacancyid = Yii::app()->request->getParam('vacancyid') )
	    {
	        $vacancyid = Yii::app()->request->getParam('vid', $vacancyid);
	    }
	    
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
		$model = new ProjectMember('search');
		// clear any default values
		$model->unsetAttributes();
		if( isset($_GET['ProjectMember']) )
		{
		    $model->attributes = $_GET['ProjectMember'];
		}

		$this->render('admin', array(
			'model' => $model,
		));
	}
	
	/**
	 * 
	 * @return void
	 */
	public function actionChangeVacancy()
	{
	    $pk        = Yii::app()->request->getParam('pk');
	    $vacancyId = Yii::app()->request->getParam('value');
	    
	    $member  = $this->loadModel($pk);
	    $vacancy = $this->loadVacancyModel($vacancyId);
	    
	    if ( $vacancy->needMoreDataFromUser($member->questionary) )
	    {// нужно добавить доп. данные перед перемещением заявки
	        $instances = ExtraFieldInstance::model()->forVacancy($vacancy)->findAll();
	        foreach ( $instances as $instance )
	        {
	            $valueExists = ExtraFieldValue::model()->forField($instance->fieldObject->id)->
                    forQuestionary($member->questionary)->forVacancy($vacancy)->exists();
	            if ( ! $valueExists )
	            {
	                $value = new ExtraFieldValue;
	                $value->instanceid    = $instance->id;
	                $value->questionaryid = $member->questionary->id;
	                $value->value         = '';
	                $value->save();
	            }
	        }
	    }
	    $member->vacancyid = $vacancy->id;
	    $member->save();
	}

	/**
	 * Отдельная страница для загрузки файла 
	 * 
	 * @return void
	 */
    public function actionUploadPage()
    {
        $this->layout = '//layouts/column1';
        
        $id     = Yii::app()->request->getParam('id');
        $member = $this->loadModel($id);
        
        $this->render('uploadPage', array(
            'member' => $member,
        ));
    } 

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * 
	 * @param integer the ID of the model to be loaded
	 * @return ProjectMember
	 */
	public function loadModel($id)
	{
		$model = ProjectMember::model()->findByPk($id);
		if( $model === null )
		{
		    throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * 
	 * @param integer the ID of the model to be loaded
	 * @return EventVacancy
	 */
	public function loadVacancyModel($id)
	{
		$model = EventVacancy::model()->findByPk($id);
		if( $model === null )
		{
		    throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) AND $_POST['ajax'] === 'project-member-form' )
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
	        throw new CHttpException(404, 'Необходимо указать статус');
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
	        'type'      => 'applications',
	    ));
	    $this->redirect($url);
	}
}