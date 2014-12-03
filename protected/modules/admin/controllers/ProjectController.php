<?php

/**
 * 
 */
class ProjectController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';
	/**
	 * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
	 */
	protected $defaultModelClass = 'Project';
	
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
			array('allow',
				'actions' => array('index','view'),
				'users'   => array('@'),
			),
			array('allow',
				'actions' => array('create','update'),
				'users'   => array('@'),
			),
			array('allow',
				'actions' => array('admin', 'delete', 'setStatus', 'uploadBanner'),
				'users' => array('@'),
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
		$model = new Project;
		$this->performAjaxValidation($model);
		
		if ( isset($_POST['Project']) )
		{
			$model->attributes = $_POST['Project'];
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create',array(
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
		$this->performAjaxValidation($model);
		
		if ( isset($_POST['Project']) )
		{
			$model->attributes = $_POST['Project'];
			if( $model->save() )
			{
				$this->redirect(array('view', 'id' => $model->id));
			}
		}
		
		$this->render('update', array(
			'model' => $model,
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
	    $this->actionAdmin();
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new Project('search');
		$model->unsetAttributes();  // clear any default values
		
		if ( isset($_GET['Project']) )
		{
		    $model->attributes = $_GET['Project'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Изменить статус проекта
	 *
	 * @param  int $id
	 * @throws CHttpException
	 * @return void
	 */
	public function actionSetStatus($id)
	{
	    $model = $this->loadModel($id);
	    if ( ! $status = Yii::app()->request->getParam('status') )
	    {
	        throw new CHttpException(400, 'Необходимо указать статус');
	    }
	    $status = 'swProject/'.$status;
	    $model->swSetStatus($status);
	    $model->save();
	
	    Yii::app()->user->setFlash('success', 'Статус изменен');
	
	    $url = Yii::app()->createUrl('/admin/project/view', array('id' => $id));
	    $this->redirect($url);
	}
	
	/**
	 * Загрузить баннер проекта
	 *
	 * @return void
	 *
	 * @todo временная функция, заменить action-классом
	 */
	public function actionUploadBanner()
	{
	    $pk        = Yii::app()->request->getParam('pk');
	    $projectId = Yii::app()->request->getParam('projectId', '0');
	     
	    // извлекаем настройку и старый баннер если он был
	    $config    = Config::model()->findByPk($pk);
	    $oldBanner = $config->getValueObject();
	     
	    // создаем модель файла во внешнем хранилище
	    $newBanner = new ExternalFile;
	    $newBannerData = array(
	        'bucket' => Yii::app()->params['AWSBucket'],
	        'path'   => 'projects/'.$projectId.'/banner',
	    );
	    // загружаем файл на S3
	    $newBanner->prepareSync($newBannerData);
	    $newBanner->saveFile();
	     
	    if ( $newBanner->save() )
	    {// обновляем значение настройки
	        $config->valueid = $newBanner->id;
	    }
	    if ( $oldBanner )
	    {// удаляем старую запись
	        $oldBanner->delete();
	    }
	    $config->save();
	     
	    $this->redirect(array('update', 'id' => $projectId));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * 
	 * @param  integer the ID of the model to be loaded
	 * @return Project
	 */
	/*public function loadModel($id)
	{
		$model = Project::model()->findByPk($id);
		if ( $model === null )
		{
		    throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}*/

	/**
	 * Performs the AJAX validation.
	 * 
	 * @param  CModel the model to be validated
	 * @return void
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'project-form' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
