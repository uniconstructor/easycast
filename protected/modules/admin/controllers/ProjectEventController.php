<?php

/**
 * Контроллер мероприятия (для администратора)
 * @todo языковые строки
 */
class ProjectEventController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';
	/**
	 * @var string
	 */
	public $defaultAction = 'create';
	/**
	 * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
	 */
	protected $defaultModelClass = 'ProjectEvent';

	/**
	 * @see CController::init()
	 */
	public function init()
	{
	    Yii::import('application.extensions.ESearchScopes.behaviors.*');
	    Yii::import('application.extensions.ESearchScopes.models.*');
	    Yii::import('application.extensions.ESearchScopes.*');
	    Yii::import('application.modules.user.*');
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
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('view', 'create', 'update', 'delete', 'setStatus', 'callList'),
				'users'   => array('@'),
			),
			array('deny', // deny all users
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
		$model = new ProjectEvent;
		
		$projectid = Yii::app()->request->getParam('projectid', 0);
		$groupid   = Yii::app()->request->getParam('parentid', 0);
		$type      = Yii::app()->request->getParam('type', 'event');
		
		if ( ! $project = Project::model()->findByPk($projectid) )
		{
		    throw new CHttpException(404, 'Необходимо указать id проекта');
		}
		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if ( isset($_POST['ProjectEvent']) )
		{
		    $_POST['ProjectEvent']['projectid'] = $projectid;
			$model->attributes = $_POST['ProjectEvent'];
			
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id' => $model->id));
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
	    $this->layout = '//layouts/column1';
	    
		$model = $this->loadModel($id);
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if ( isset($_POST['ProjectEvent']) )
		{
			$model->attributes = $_POST['ProjectEvent'];
			if ( $model->save() )
			{
			    $this->redirect(array('view','id' => $model->id));
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
		if ( Yii::app()->request->isPostRequest )
		{// we only allow deletion via POST request
			$this->loadModel($id)->delete();
			if ( ! isset($_GET['ajax']) )
			{// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		}else
		{
		    throw new CHttpException(400, 'Invalid request. Your action has been logged.');
		}
	}
	
	/**
	 * Изменить статус мероприятия
	 * @param int $id - id мероприятия
	 * @throws CHttpException
	 * @return null
	 */
	public function actionSetStatus($id)
	{
	    $model = $this->loadModel($id);
	    if ( ! $status = Yii::app()->request->getParam('status') )
	    {
	        throw new CHttpException(400, 'Необходимо указать статус');
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

	/**
	 * Отобразить вызывной лист
	 * @return null
	 */
	public function actionCallList()
	{
	    Yii::import('reports.models.*');
	    // Отображаем фотовызывной в одну колонку
	    $this->layout = '//layouts/column1';
	    // получаем id мероприятия, если вызывной лист создается для мероприятия
	    $eventId  = Yii::app()->request->getParam('eventId', 0);
	    // получаем id отчета (если отображается существующий отчет)
	    $reportId = Yii::app()->request->getParam('id', 0);
	    // определяем на каком языке формировать фотовызывной
	    $language = Yii::app()->request->getParam('language', 'ru');
	    
	    if ( ! $report = RCallList::model()->findByPk($reportId) )
	    {// будем создавать новый вызывной лист
	        /* @var $event ProjectEvent */
	        if ( ! $event = ProjectEvent::model()->findByPk($eventId) AND ! $reportId )
	        {
	            throw new CException('Невозможно отобразить вызывной лист: мероприятие не найдено');
	        }
	        $report = new RCallList;
	    }else
	    {// отображаем существующий вызывной лист
	        $event = $this->loadModel($report->reportData['event']->id);
	    }
	    
	    if ( $attributes = Yii::app()->request->getParam('RCallList') )
	    {// сохраняем вызывной лист - собираем все данные, сериализуем и сохраняем запись 
	        $report->attributes = $attributes;
	        // определяем стандартное название фотовызывного, на сулчай если оно не задано вручную
	        // (а оно в большинстве случаев не задано вручную) 
	        $defaultReportName = AdminModule::t('call_list').' '.$event->getFormattedTimePeriod();
	        // определяем, анкеты с какими статусами входят в отчет
	        $statuses    = Yii::app()->request->getParam('statuses', array('active'));
	        // получаем перевод ролей, мероприятия и проекта (если требуется)
	        $translation = Yii::app()->request->getParam('translation', array());
	        //CVarDumper::dump($_POST, 10, true);die;
	        
	        $options  = array(
	            'event'       => $event,
	            'statuses'    => $statuses,
	            'language'    => $language,
	            'translation' => $translation,
	        );
	        
	        if ( $language === 'en' )
	        {// формируем фотовызывной на английском языке: временно переключаем на английский все приложение
    	        Yii::app()->setLanguage('en');
	        }
	        if ( ! trim($report->name) )
	        {// название фотовызывного не задано - используем стандартное
	           $report->name = $defaultReportName;
	        }
	        // собираем и сохраняем данные для отчета
	        $report->createReport($options);
	        // фотовызыной сформирован, переключаем язык обратно на русский
	        Yii::app()->setLanguage('ru');
	        
	        // после сохранения вызывного листа - переходим на страницу просмотра того что создалось
	        Yii::app()->user->setFlash('success', 'Вызывной лист создан.<br>Теперь его можно отправить по почте.');
	        $this->redirect(Yii::app()->createUrl('/admin/projectEvent/callList', array('id' => $report->id)));
	    }
	    if ( $email = trim(Yii::app()->request->getParam('email', '')) )
	    {// отправляем вызывной лист по почте
	        $showContacts = Yii::app()->request->getParam('showContacts', false);
	        $castingList  = Yii::app()->request->getParam('castingList', false);
	        if ( $castingList )
	        {// преобразуем в кастинг-лист (включаем максимальное количество данных)
	            $this->sendCastingList($report, $email, $showContacts);
	        }else
	        {// обычный вызывной лист (минимум данных)
	            $this->sendCallList($report, $email, $showContacts);
	        }
	        
	        Yii::app()->user->setFlash('success', "Вызывной лист отправлен на <b>{$email}</b>.");
	        if ( $showContacts )
	        {// дополнительно напомним о том, что вызывной лист отправлен с контактами
	            Yii::app()->user->setFlash('info', "В письме были указаны контакты актеров.");
	        }
	    }
	    // условие, по которому отображается список ранее созданных вызывных
	    $reportListCriteria = new CDbCriteria();
	    $reportListCriteria->scopes = array('forEvent' => $event->id);
	    
	    $this->render('callList', array(
	        'event'  => $event,
	        'report' => $report,
	        'reportListCriteria' => $reportListCriteria,
	    ));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 * @return ProjectEvent 
	 */
	/*public function loadModel($id)
	{
		$model = ProjectEvent::model()->findByPk($id);
		if ( $model === null )
		{
		    throw new CHttpException(404, Yii::t('coreMessages', 'the_requested_page_does_not_exist'));
		}
		return $model;
	}*/

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) && $_POST['ajax'] === 'project-event-form' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Отправить вызывной лист по почте
	 * @param RCallList $report - id вызывной лист
	 * @param string $email
	 * @param string $showContacts
	 * @return null
	 */
	protected function sendCallList($report, $email, $showContacts=false)
	{
	    $eventId = $report->reportData['event']->id;
	    $module  = Yii::app()->getModule('mailComposer');
	    $message = $module::getMessage('callList', array('callList' => $report, 'addContacts' => $showContacts));
	    UserModule::sendMail($email, $report->name, $message, true);
	}
	
	/**
	 * Отправить кастинг-лист по почте
	 * @param RCallList $report - id вызывной лист
	 * @param string $email
	 * @param string $showContacts
	 * @return null
	 */
	protected function sendCastingList($report, $email, $showContacts=false)
	{
	    $eventId = $report->reportData['event']->id;
	    $module  = Yii::app()->getModule('mailComposer');
	    $message = $module::getMessage('castingList', array('castingList' => $report, 'addContacts' => $showContacts));
	    UserModule::sendMail($email, 'Кастинг-лист', $message, true);
	}
}