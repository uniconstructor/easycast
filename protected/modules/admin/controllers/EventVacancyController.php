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
	public $layout = '//layouts/column2';
	
	/**
	 * @see CController::actions()
	 */
	public function actions()
	{
	    return array(
	        // создать элемент оповещения
	        'createBlockItem' => array(
	            'class'      => 'application.actions.EcCreateAction',
	            'modelName'  => 'EasyListItem',
	        ),
	        // редактировать элемент оповещения
	        'updateBlockItem' => array(
	            'class'      => 'application.actions.EcUpdateAction',
	            'modelName'  => 'EasyListItem',
	        ),
	        // удалить элемент оповещения
	        'deleteBlockItem' => array(
	            'class'      => 'application.actions.EcDeleteAction',
	            'modelName'  => 'EasyListItem',
	        ),
	    );
	}

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
	 * @see CController::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        'wizard' => array(
	            'class' => 'ext.WizardBehavior',
	            'steps' => array(
	                'Начало' => 'Intro',
	                'Основная информация' => 'BaseInfo',
	                'Критерии поиска' => 'SearchData',
	                'Разбиение заявок на группы' => 'AddSections',
	                'Настройка групп заявок' => 'ConfigureSections',
	                'Выбор обязательных полей' => 'AddRequired',
	                'Настройка обязательных полей' => 'ConfigureRequired',
	                'Добавление дополнительных полей' => 'AddExtra',
	                'Настройка дополнительных полей' => 'ConfigureExtra',
	                'Создание формы регистрации' => 'AddForm',
	                'Настройка формы регистрации' => 'ConfigureForm',
	                'Внешний вид заявки' => 'MemberDisplay',
	                'Фильтры заявок' => 'Filters',
	                'Проверка' => 'Success',
	            ),
	            'autoAdvance' => true,
	        ),
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
			    'actions' => array(
                    'view', 'create', 'update', 'delete', 'wizard',
			        'setStatus', 'setSearchData', 'ClearFilterSearchData',
			        'createBlockItem', 'updateBlockItem', 'deleteBlockItem',
			        'restoreDefault'
			    ),
				'users'   => array('@'),
			),
		    // запрещаем все остальное
			array('deny', 
			    'users'   => array('*'),
			),
		);
	}
	
	/**
	 * Обработка одного шага формы
	 * @param string $step
	 * @return void
	 */
	public function actionWizard($step=null)
	{
	    $this->layout = '//layouts/containerAltertnate';
	    
	    $menuOptions = array(
	        'type'    => TbMenu::TYPE_TABS,
	        'stacked' => true,
	    );
	    $menu = $this->createWidget('bootstrap.widgets.TbMenu', $menuOptions);
	    $this->setMenu($menu);
	    
	    $this->process($step);
	}
	
	/**
	 * Начало работы по заполнению формы
	 * @param WizardEvent $event
	 * @return void
	 */
	public function wizardStart($event)
	{
	    $event->handled = true;
	}
	
	/**
	 * Обработка события "завершен шаг": отвечает за отрисовку и проверку формы, 
	 * а также засохранение данных в базу
	 * @param WizardEvent $event - данные одного шага формы
	 * @return void
	 */
    public function wizardProcessStep($event)
    {
        $name = 'process'.$event->step;
        if ( ! method_exists($this, $name) )
        {// неверно указан этап заполнения
            throw new CException(Yii::t('yii', '{class} does not have a methodnamed "{name}".',
                array('{class}' => get_class($this), '{name}' => $name)));
        }
        $event->handled = call_user_func(array($this, $name), $event);
    }
    
    /**
     * Обработка события "неправильно указан следующий шаг"
     * @param WizardEvent $event - данные одного шага формы
     * @return void
     */
    public function wizardInvalidStep($event)
    {
        Yii::app()->getUser()->setFlash('error', $event->step.': неправильно указан этап заполнения формы');
    }
    
    /**
     * Обработка события "сохранить черновик введенных данных". 
     * @param WizardEvent $event - данные одного шага формы
     * @return void
     */
    public function wizardSaveDraft($event)
    {
        //$user = new User();
        //$uuid = $user->saveRegistration($event->data);
        //$event->sender->reset();
		//$this->render('wizard/draft', compact('uuid'));
		$this->render('wizard/draft');
		Yii::app()->end();
    }
    
    /**
     * Обработка окончательного сохранения 
     * @param WizardEvent $event - данные одного шага формы
     * @return void
     */
    public function wizardFinished($event)
    {
        $event->handled = true;
        
        if ( $event->step === true )
        {// процесс успешно завершен
            $this->render('wizard/completed', compact('event'));
        }else
        {// процес не удалось завершить
            $this->render('wizard/finished', compact('event'));
        }
        // очистка данных модели
        $event->sender->reset();
        
        Yii::app()->end();
    }
    
    // Шаги создания роли // 
    
    /**
     * Обработка первого шага - статическая страница
     * @param WizardEvent $event
     * @return bool
     */
    public function processIntro($event)
    {
        //CVarDumper::dump($event);
        //CVarDumper::dump($_POST);
        if ( Yii::app()->request->getPost('startWizard') )
        {
            $event->sender->save(array('eventid' => 999999, 'vacancyid' => 0));
            return true;
        }else
        {
            $this->render('wizard/intro');
        }
    }
    
    /**
     *
     * @param WizardEvent $event
     * @return bool
     */
    public function processBaseInfo($event)
    {
        $vacancy = new EventVacancy();
        if ( $event->data )
        {
            $vacancy->attributes = $event->data;
        }
        if ( Yii::app()->request->getPost('EventVacancy') )
        {
            return true;
        }else
        {
            $this->render('wizard/baseInfo', array('model' => $vacancy));
        }
    }
    
    /**
     *
     * @param WizardEvent $event
     * @return bool
     */
    public function processAddSections($event)
    {
        return true;
    }
    
    /**
     *
     * @param WizardEvent $event
     * @return bool
     */
    public function processConfigureSections($event)
    {
        return true;
    }
    
    /**
     *
     * @param WizardEvent $event
     * @return bool
     */
    public function processAddRequired($event)
    {
        return true;
    }
    
    /**
     *
     * @param WizardEvent $event
     * @return bool
     */
    public function processConfigureRequired($event)
    {
        return true;
    }
    
    // Остальные действия //
    
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
	    $this->layout = '//layouts/column1';
	    
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
	    $this->layout = '//layouts/column1';
		
		if ( ! $eventid = Yii::app()->request->getParam('eventid') )
		{// для какого события создается роль
		    throw new CHttpException(404, 'Необходимо указать id события');
		}
		$model = new EventVacancy;
		$event = ProjectEvent::model()->findByPk($eventid);
		
		// AJAX-проверка введенных значений
		$this->performAjaxValidation($model);

		if ( isset($_POST['EventVacancy']) )
		{
		    $_POST['EventVacancy']['eventid'] = $eventid;
			$model->attributes = $_POST['EventVacancy'];
			if ( $model->save() )
			{
			    $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create',array(
			'model' => $model,
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
	    $this->layout = '//layouts/column1';
	    
		$model = $this->loadModel($id);
		$step  = Yii::app()->request->getParam('step');

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
		
		if ( isset($_POST['EventVacancy']) )
		{
			$model->attributes = $_POST['EventVacancy'];
			if ( $model->save() AND ! Yii::app()->request->isAjaxRequest )
			{// перенаправляем участника на сраницу просмотра 
			    // (если это не сохранение через AJAX)
			    $this->redirect(array('view', 'id' => $model->id));
			}
		}
		if ( ! Yii::app()->request->isAjaxRequest )
		{
		    $this->render('update', array(
		        'model' => $model,
		        'step'  => $step,
		    ));
		}else
		{
		    echo 'OK';
		}
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
		    throw new CHttpException(400, 'Invalid request.');
		}
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
	    // $id = Yii::app()->request->getParam('id', 0);
	    $id = Yii::app()->request->getParam('searchObjectId', 0);
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
	    if ( ! $namePrefix = Yii::app()->request->getPost('namePrefix', '') )
	    {// если название фильтра не задано - мы не можем его очистить
	        echo 'namePrefix required';
	        return;
	    }
	    
	    // $vacancyId = Yii::app()->request->getPost('id', 0);
	    $vacancyId = Yii::app()->request->getPost('searchObjectId', 0);
	    if ( $vacancy = EventVacancy::model()->findByPk($vacancyId) )
	    {// очищаем данные внутри вакансии
	        $vacancy->clearFilterSearchData($namePrefix);
	    }
	     
	    echo 'OK';
	    Yii::app()->end();
	}
	
	/**
	 * 
	 * 
	 * @return void
	 * 
	 * @todo
	 */
	public function actionRestoreDefault()
	{
	    $restore = Yii::app()->request->getParam('restoreNotificationConfig', 0);
	    $id      = Yii::app()->request->getParam('id', 0);
	    
	    if ( $restore AND $id )
	    {
	        $model  = $this->loadModel($id);
	        $config = $model->getConfigObject('inviteNotificationList');
	        $config->restoreDefault();
	        
	        $this->redirect(array('update', 'id' => $id));
	    }
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
		    throw new CHttpException(404, 'Vacancy not found (id='.$id.')');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) AND $_POST['ajax'] === 'event-vacancy-form' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}