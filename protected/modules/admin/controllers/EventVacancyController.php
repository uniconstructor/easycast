<?php

/**
 * Контроллер для администрирования вакансий вакансий мероприятия 
 */
class EventVacancyController extends Controller
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
	 * 
	 * @todo настроить проверку прав на основе ролей
	 */
	public function accessRules()
	{
		return array(
		    // разрешаем выполнять любые действия только авторизованным пользователям
		    // проверка на админа происходит в модуле admin
			array('allow',
				'actions' => array('index','view', 'create','update','admin','delete','setStatus',
				    'setSearchData', 'ClearFilterSearchData'),
				'users'   => array('@'),
			),
		    // запрещаем все остальное
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
		$model=new EventVacancy;
		
		if ( ! $eventid = Yii::app()->request->getParam('eventid') )
		{
		    throw new CHttpException(404,'Необходимо указать id события');
		}
		
		$event = ProjectEvent::model()->findByPk($eventid);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['EventVacancy']))
		{
		    $_POST['EventVacancy']['eventid'] = $eventid;
			$model->attributes=$_POST['EventVacancy'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		    'event' => $event,
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

		if(isset($_POST['EventVacancy']))
		{
			$model->attributes=$_POST['EventVacancy'];
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
		if ( Yii::app()->request->isPostRequest )
		{
			// we only allow deletion via POST request
			$vacancy = $this->loadModel($id);
			$eventId = $vacancy->eventid;
			$vacancy->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if( ! isset($_GET['ajax']) )
			{
			    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/admin/projectEvent/view', 'id' => $eventId));
			}
		}else
		{
		    throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 * 
	 * @todo не используется, удалить при рефакторинге
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('EventVacancy');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 * 
	 * @todo не используется, удалить при рефакторинге
	 */
	public function actionAdmin()
	{
		$model=new EventVacancy('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['EventVacancy']))
			$model->attributes=$_GET['EventVacancy'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/**
	 * Изменить статус объекта
	 * @param int $id
	 * @throws CHttpException
	 */
	public function actionSetStatus($id)
	{
	    $model = $this->loadModel($id);
	    if ( ! $status = Yii::app()->request->getParam('status') )
	    {
	        throw new CHttpException(404,'Необходимо указать статус');
	    }
	    $model->setStatus($status);
	     
	    Yii::app()->user->setFlash('success', 'Статус изменен');
	     
	    $url = Yii::app()->createUrl('/admin/eventVacancy/view', array('id' => $id));
	    $this->redirect($url);
	}
	
	/**
	 * Изменить критерии выборки людей, которые подходят под эту вакансию
	 * 
	 * @return null
	 */
	public function actionSetSearchData()
	{
	    if ( ! Yii::app()->request->isAjaxRequest )
	    {
	        Yii::app()->end();
	    }
	    // id вакансии (обязательно)
	    $id = Yii::app()->request->getParam('id', 0);
	    $vacancy = $this->loadModel($id);
	    
	    if ( $vacancy->status != EventVacancy::STATUS_DRAFT )
	    {// менять критерии подбора людей в вакансию можно только в статусе "Черновик"
	        echo 'Менять критерии подбора людей в вакансию можно только в статусе "Черновик"'; 
	        Yii::app()->end();
	    }
	    
	    // условия, по которым отбираются люди на вакансию
	    if ( $data = Yii::app()->request->getPost('data', null) AND ! empty($data) )
	    {// переданы данные для поиска - делаем из них нормальный массив
	        $data = CJSON::decode($data);
	    }else
	    {// данные для поиска не переданы - завершаем работу
	        echo 'Не переданы данные для поиска';
	        Yii::app()->end();
	    }
	    
	    // Сохраняем данные поисковой формы и пересоздаем критерий поиска
	    $vacancy->setSearchData($data);
	    
	    // считаем сколько участников подошло под выбранные критерии
	    echo 'Подходящих участников: '.$vacancy->countPotentialApplicants();
	    Yii::app()->end();
	}
	
	/**
	 * Очистить сохраненные в сессии данные формы поиска
	 * (Может очистить как данные всей формы поиска так и в отдельном разделе.
	 * Как все поля - так и отдельное поле, все зависит от настроек)
	 *
	 * @return null
	 * @todo обработать возможные ошибки
	 */
	public function actionClearFilterSearchData()
	{
	    if ( ! Yii::app()->request->isAjaxRequest )
	    {
	        Yii::app()->end();
	    }
	    // если название фильтра не задано - мы можем очистить только
	    if ( ! $namePrefix = Yii::app()->request->getPost('namePrefix', '') )
	    {
	        throw new CHttpException(500, 'namePrefix required');
	    }
	    
	    $vacancyId = Yii::app()->request->getPost('id', 0);
	    if ( $vacancy = EventVacancy::model()->findByPk($vacancyId) )
	    {// очищаем данные внутри вакансии
	        $vacancy->clearFilterSearchData($namePrefix);
	    }
	     
	    echo 'OK';
	    Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 * 
	 * @return EventVacancy
	 */
	public function loadModel($id)
	{
		$model = EventVacancy::model()->findByPk($id);
		if ( $model === null )
		{
		    throw new CHttpException(404,'Vacancy not found (id='.$id.')');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='event-vacancy-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
