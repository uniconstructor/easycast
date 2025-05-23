<?php

/**
 * Контроллер анкеты пользователя
 * Работает с формой анкеты пользователя
 *
 * Форма анкеты состоит из множества полей.
 * Простые поля хранятся в таблице questionary
 *
 * Сложные поля хранятся в отдельных таблицах и привязаны к анкете. 
 * За редактирование, добавление и удаление каждого сложного отвечает отдельная модель и отдельный контроллер
 * Контроллер анкеты отвечает, в основном, только за поля модели Questionary
 */
class QuestionaryController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column1';
	
	/**
	 * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
	 */
	protected $defaultModelClass = 'Questionary';
	
	/**
	 * @see CController::init()
	 */
	public function init()
	{
	    Yii::app()->getComponent('ecawsapi')->getS3();
	    parent::init();
	}
	
	/**
	 * @return array
	 * 
	 * @todo настроить проверку прав на основе RBAC
	 */
	public function filters()
	{
	    $baseFilters = parent::filters();
	    $newFilters  = array(
	        'accessControl',
	        'postOnly + delete',
	        // фильтр для подключения YiiBooster 3.x (bootstrap 2.x)
	        array(
	            'ext.bootstrap.filters.BootstrapFilter - delete, loginAs, upload',
	        ),
	    );
	    return CMap::mergeArray($baseFilters, $newFilters);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * 
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
			    'actions' => array('index', 'view', 'catalog', 'ajaxGetUserInfo', 'invite', 
			        'dismiss', 'userActivation',
			    ),
				'users'   => array('*'),
			),
			array('allow',
				'actions' => array('update', 'ajax', 'toggleProjectTypeNotification'),
				'users'   => array('@'),
			),
			array('allow',
				'actions' => array('delete', 'loginAs', 'upload'),
				'roles'   => array('admin'),
			),
			array('deny',
				'users' => array('*'),
			),
		);
	}
	
	/**
	 * @see CController::actions()
	 */
	public function actions()
	{
	    return array(
	        'upload' => array(
	            'class' => 'xupload.actions.S3XUploadAction',
	        ),
	    );
	}

	/**
	 * Отображает анкету пользователя
	 * 
	 * @param integer $id - id отображаемой анкеты
	 */
	public function actionView($id = null)
	{
	    if ( ! $id )
	    {// id анкеты не передано - считаем, что пользователь
	        // хочет просмотреть свою страницу и пробуем определить id самостоятельно
	        if ( Yii::app()->user->isGuest )
	        {// У гостя не может быть своей страницы: redirect на вход/регистрацию
	            Yii::app()->user->loginRequired();
	        }
	        $id = Yii::app()->getModule('user')->user()->questionary->id;
	    }
	    // если нужно открыть анкету на конкретной вкладке
	    $activeTab = Yii::app()->request->getParam('activeTab', 'main');
	    // загружаем анкету, которую будем просматривать
	    $questionary = $this->loadModel($id);
	    // берем
	    
	    
	    if ( ! Yii::app()->user->isGuest AND 
	         ! Yii::app()->getModule('user')->user()->questionary->timemodified AND
	         ! Yii::app()->user->checkAccess('Admin') )
	    {// анкета участника еще не заполнена - и он пользователь или актер - перенаправляем его на страницу анкеты
	        $this->redirect(Yii::app()->createUrl('//questionary/questionary/update',
	                array('id' => Yii::app()->getModule('user')->user()->questionary->id)));
	    }
	    // Проверяем права для отображения кнопки редактирования анкеты
	    $canEdit = $this->canEditUser($questionary->user->id);
	    
	    // И кнопок приглашений
	    $inviteButton  = '';
	    $dismissButton = '';
	    $orderMessage  = '';
	    $orderMessageClass = '';
	    $orderMessageStyle = '';
        if ( $this->canInviteUser() )
        {// у пользователя есть право приглашать участников
            if ( ! FastOrder::alreadyInOrder($id) )
            {// участник еще не приглашен - выводим кнопку приглашения
                $inviteButton = $this->createCustomerButton($id, 'invite');
                $orderMessageClass = '';
                $orderMessageStyle = 'display:none;';
                $orderMessage = '';
            }else
            {// участник уже приглашен - выводим кнопку отмены заказа
                $dismissButton = $this->createCustomerButton($id, 'dismiss');
                $orderMessageClass = 'alert alert-info';
                $orderMessageStyle = '';
                $myChoiceLink = CHtml::link(Yii::t('coreMessages', 'mainmenu_item_my_choice'), 
                    Yii::app()->createUrl('//catalog/catalog/myChoice'), array('target' => '_blank'));
                $orderMessage = QuestionaryModule::t('already_invited_message', array('{link}' => $myChoiceLink));
            }
        }
	    
	    if ( $questionary->visible OR $canEdit )
	    {// анкета участника открыта и доступна в поиске
	        // (скрытые анкеты также видны самим участникам и админам)
	        $this->render('view', array(
	            'questionary'       => $questionary,
	            'address'           => $questionary->address,
	            'canEdit'           => $canEdit,
	            'dismissButton'     => $dismissButton,
	            'inviteButton'      => $inviteButton,
	            'orderMessage'      => $orderMessage,
	            'orderMessageClass' => $orderMessageClass,
	            'orderMessageStyle' => $orderMessageStyle,
	            'activeTab'         => $activeTab,
	        ));
	    }else
	    {// анкета скрыта и прав для просмотра нет - выводим заглушку
	        $this->render('hidden');
	    }
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
    {
        // Загружаем модели изменяемых элементов
        $questionary = $this->loadModel($id);
        //$questionary->setScenario('update');
        $user        = $questionary->user;
        
        if ( ! Yii::app()->user->checkAccess('Admin') AND Yii::app()->user->id != $user->id )
        {// проверяем права на редактирование анкеты
            $this->redirect(Yii::app()->getModule('questionary')->profileUrl);
        }
        
        // AJAX-а вводимых значений в процессе заполнения формы
        $this->performAjaxValidation($questionary);
        
        if ( ! $address = $questionary->address )
        {// проверка, на случай если в базе ошибки и адрес куда-то пропал
            $address = new Address();
            $address->objectid   = $questionary->id;
            $address->objecttype = 'questionary';
            $address->save();
        }
        if ( ! $recordingConditions = $questionary->recordingconditions )
        {// такая же проверка для условий участия в съемках
            $recordingConditions = new QRecordingConditions();
            $recordingConditions->questionaryid = $questionary->id;
            $recordingConditions->save();
        }
        
        if ( $attributes = Yii::app()->request->getPost('Questionary') )
        {
            // получаем данные анкеты
            $questionary->attributes = $attributes;
            //$user->attributes        = Yii::app()->request->getPost('User');
            // получаем данные адреса
            //$address->attributes     = Yii::app()->request->getPost('Address');
            if ( $questionary->save() )
            {// записываем в базу значения анкеты и адреса
                if ( $recordingConditions->attributes = Yii::app()->request->getPost('QRecordingConditions') )
                {// Получаем условия участия в съемках
                    $recordingConditions->save();
                }
                //$user->save();
                //$address->save();
                $this->redirect(array('view', 'id' => $questionary->id));
            }
        }

        // отображение формы редактирования
        $this->render('update', array(
            // передаем основные данные
            'questionary'         => $questionary,
	        'address'             => $address,
            'user'                => $user,
            'recordingConditions' => $recordingConditions,
        ));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 * 
	 * @todo заменить реальное удаление на смену статуса в "удален"
	 * @todo сделать отдельную страницу удаления анкеты с вводом пароля
	 */
	public function actionDelete($id)
	{
	    if ( ! Yii::app()->user->checkAccess('Admin') )
	    {// никто кроме админа не може удалять анкеты
	        $this->redirect(Yii::app()->baseUrl);
	    }
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if ( ! isset($_GET['ajax']) )
		{
		    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('//admin/questionary'));
		}
	}

	/**
	 * Действие по умолчанию (отображаем анкету пользователя)
	 */
	public function actionIndex($id = null)
	{
	    $this->actionView($id);
	}
	
    /**
     * Пригласить пользователя (работает через AJAX-запрос)
     * Добавляет анкету пользователя в формирующийся в сессии заказ
     * 
     * @return string - json-ответ с результатом
     * 
     * @todo языковые строки
     * @todo в сообщении писать кто именно приглашен и склонять сообщение в зависимости от пола
     */
    public function actionInvite()
    {
        $result = array('error' => '');
        if ( ! $this->canInviteUser() )
        {// нет прав на приглажение пользователя
            $result['error']   = 'NOACCESS';
            $result['message'] = QuestionaryModule::t('no_invite_access_message');
            echo CJSON::encode($result);
            Yii::app()->end();
        }
        // получаем id анкеты
        $id = Yii::app()->request->getParam('id');
        
        if ( ! $questionary = Questionary::model()->findByPk($id) )
        {// нет такого участника
            $result['error']   = 'NOTFOUND';
            $result['message'] = QuestionaryModule::t('user_not_found');
            echo CJSON::encode($result);
            Yii::app()->end();
        }
        
        $fullname = 'Участник';
        if ( $questionary->firstname AND $questionary->lastname )
        {
            $fullname = $questionary->user->fullname;
        }
        
        // участник найден - добавляем его в заказ
        FastOrder::addToOrder($id);
        $myChoiceLink = CHtml::link(Yii::t('coreMessages','mainmenu_item_my_choice'),Yii::app()->createUrl('//catalog/catalog/myChoice'));
        // сообщаем об этом пользователю
        $result['message'] = QuestionaryModule::t('already_invited_message', array('{link}' => $myChoiceLink));
        
        echo CJSON::encode($result);
        Yii::app()->end();
    }
    
    /**
     * Удалить пользователя из заказа
     * 
     * @return string - json-ответ с результатом
     */
    public function actionDismiss()
    {
        $result = array('error' => '');
        if ( ! $this->canInviteUser() )
        {// нет прав на приглажение пользователя
            $result['error'] = 'NOACCESS';
            $result['message'] = QuestionaryModule::t('no_invite_access_message');
            echo CJSON::encode($result);
            Yii::app()->end();
        }
        // получаем id анкеты
        $id = Yii::app()->request->getParam('id');
        
        // Удаляем участника из заказа
        FastOrder::removeFromOrder($id);
        
        if ( ! $questionary = Questionary::model()->findByPk($id) )
        {// нет такого участника
            $result['message'] = QuestionaryModule::t('dismiss_message');
            echo CJSON::encode($result);
            Yii::app()->end();
        }
        
        $fullname = 'Участник';
        if ( $questionary->firstname AND $questionary->lastname )
        {
            $fullname = $questionary->user->fullname;
        }
        
        // @todo сообщать кто именно удален из заказа
        $result['message'] = QuestionaryModule::t('dismiss_message');
        
        echo CJSON::encode($result);
        Yii::app()->end();
    }
    
    /**
     * Получить html-код краткого, либо полного описания участника
     * 
     * @return string - html-код с полной информацией о пользователе
     * @todo свести отображение любой информации о пользователе к плагину QUserInfo и
     *        избавиться от посторонних view-файлов
     * @todo проверять статус анкеты перед отображением
     */
    public function actionAjaxGetUserInfo()
    {
        $result = '';
        // получаем id анкеты
        $id = Yii::app()->request->getParam('id');
        $questionary = $this->loadModel($id);
        
        // Формат отображения данных анкеты: зависит от того, куда загружаются данные
        // (виджет "мой выбор", результаты поиска, и т. д.)
        // @todo сделать параметр обязательным
        $displayType = Yii::app()->request->getParam('displayType', 'myChoice');
        
        // отображаем всю нужную информацию в ответ на запрос
        $this->widget('application.modules.questionary.extensions.widgets.QAjaxUserInfo.QAjaxUserInfo', array(
            'id'          => $id,
            'displayType' => $displayType,
        ));
        
        Yii::app()->end();
    }
    
    /**
     * Активация анкет, созданных без предварительного обзвона участников
     * @return null
     */
    public function actionUserActivation()
    {
        // получаем id анкеты
        $id  = Yii::app()->request->getParam('id');
        $key = Yii::app()->request->getParam('key');
        if ( ! $id OR ! $key )
        {
            throw new CHttpException('400', 'Не переданы обязательные параметры');
        }
        if ( ! $questionary = Questionary::model()->findByPk($id) )
        {
            throw new CHttpException('500', 'Анкета на найдена');
        }
        if ( $questionary->user->activkey != $key )
        {
            throw new CHttpException('400', 'Неправильная ссылка активации');
        }
        // активируем анкету
        $questionary->setStatus(Questionary::STATUS_ACTIVE);
        
        // отсылаем письмо с подтверждением и паролем
        $password = $questionary->user->generatePassword();
        $questionary->user->password = Yii::app()->getModule('user')->encrypting($password);
        $questionary->user->activkey = Yii::app()->getModule('user')->encrypting(microtime().$password);
        if ( ! $questionary->user->save() )
        {// @todo языковые строки
            throw new CHttpException(500, 'Ошибка при активации учетной записи');
        }
        $this->sendActivateConirmationEmail($questionary, $password);
        
        // авторизуем пользователя
        Yii::app()->getModule('user')->forceLogin($questionary->user);
        // отображаем страницу с результатом активации
        $url = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $questionary->id));
        $this->render('userActivation', array(
            'url' => $url,
        ));
    }
    
    /**
     * Зайти в систему под указанным участником
     * 
     * @return void
     * 
     * @todo добавить проверку CSRF
     */
    public function actionLoginAs()
    {
        $id  = Yii::app()->request->getParam('id', 0);
        if ( ! $questionary = Questionary::model()->findByPk($id) )
        {
            throw new CHttpException(400, 'Не найден участник (id='.$id.')');
            Yii::app()->end();
        }
        if ( Yii::app()->user->checkAccess('Admin') )
        {// еще одна проверка прав перед входом под учетной записью участника
            Yii::app()->getModule('user')->forceLogin($questionary->user, true);
        }
        $url = Yii::app()->createUrl('/questionary/questionary/view', array('id' => $id));
        $this->redirect($url);
    }
    
    /**
     * Изменить настройку оповещения для проекта
     * 
     * @return void
     */
    public function actionToggleProjectTypeNotification()
    {
        /* @var $config Config */
        $qid         = Yii::app()->request->getPost('objectId', 0);
        $configName  = Yii::app()->request->getPost('configName', '');
        $itemId      = Yii::app()->request->getPost('itemId', 0);
        /* @var $questionary Questionary */
        $questionary = $this->loadModel($qid);
        /* @var $item EasyListItem */
        $item        = $this->loadModel($itemId, 'EasyListItem');
        // проверка параметров
        if ( ! $configName )
        {
            throw new CHttpException(400, 'Не передано название настройки');
        }
        if ( ! $this->canEditUser($questionary->userid) )
        {
            throw new CHttpException(403, Yii::t('coreMessages', 'access_denied'));
        }
        // получаем настройку, привязанную к модели, либо создаем ее из корневой настройки
        // (в случае если эта настройка для этой модели ни разу не редактировалась)
        if ( ! $config = $questionary->getEditableConfig($configName) )
        {// настройка для этой анкеты еще не создана
            throw new CHttpException(500, 'Не удалось привязать новый экземпляр настройки к модели');
        }
        // изменяем ее значение
        $questionary->toggleConfigOption($configName, $item);
        if ( $config->hasSelectedOption($item) )
        {
            echo '['.Yii::t('coreMessages', 'config_saved').']<br><b>'.$item->title.'</b>'.
                ' больше не появится в списке ваших приглашений';
        }else
        {
            echo '['.Yii::t('coreMessages', 'config_saved').']<br>Если у нас будет <b>'.
                mb_strtolower($item->title, 'utf-8').'</b> '.
                'с подходящей ролью - мы обязательно сообщим';
        }
        Yii::app()->end();
    }
    
    /**
     * Отказаться от дальнейших оповещений по id проекта или по типу проекта
     * 
     * @return void
     * 
     * @todo если одновременно передан и тип и id проекта - то отключить оповещение для всех
     *       проектов указанного типа кроме переданного (внести его в белый список)
     */
    public function actionDisableInvites()
    {
        $projectType   = Yii::app()->request->getPost('projectType');
        $projectId     = Yii::app()->request->getPost('projectId', 0);
        $questionaryId = Yii::app()->request->getPost('questionaryId', 0);
        /* @var $questionary Questionary */
        $questionary   = $this->loadModel($questionaryId);
        /* @var $project Project */
        $project       = $this->loadModel($projectId, 'Project');
        /* @var $config Config */
        $config        = $questionary->getEditableConfig('projectTypesBlackList');
        
        throw new CException('UNFINISHED');
    }
    
	/**
	 * Performs the AJAX validation.
	 * 
	 * @param  CModel|Questionary the model to be validated
	 * @return void
	 */
	protected function performAjaxValidation($model)
	{
		if ( isset($_POST['ajax']) AND $_POST['ajax'] === 'questionary-form' )
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/// устаревшие методы ///
	
	/**
	 * Получить список городов для AJAX-подсказки в анкете.
	 *
	 * @deprecated
	 */
	public function actionAjax()
	{
	    if( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
	    {
	        // Yii::app()->end();
	    }
	    // @todo заменить обращением к geoNames
	    Yii::import('ext.CountryCitySelectorRu.*');
	    $selector = new CountryCitySelectorRu();
	    // request type ('city' ot 'region')
	    $type       = Yii::app()->request->getParam('type');
	    // counry or region
	    $parentType = Yii::app()->request->getParam('parenttype');
	    // record id or country code
	    $parentId   = Yii::app()->request->getParam('parentid');
	    // first letters
	    $term       = Yii::app()->request->getParam('term');
	
	    switch ( $type )
	    {
	        case 'city':
	            $records = $selector->getCities($parentType, $parentId, $term);
	            $result  = $selector->getAutocompleteOptions($records);
            break;
	        case 'region':
	            $records = $selector->getRegions($parentId);
	            $result  = $selector->getDropDownOptions($records);
            break;
	    }
	    echo json_encode($result);
	    Yii::app()->end();
	}
	
	/**
	 * Узнать, может ли текущий пользователь приглашать актера на съемки
	 * 
	 * (приглашать на съемки может только гость (он может быть заказчиком) 
	 * зарегистрированный заказчик или админ)
	 * 
	 * @return bool
	 * 
	 * @deprecated ииспользовать собственные роли RBAC
	 */
	protected function canInviteUser()
	{
	    if ( Yii::app()->user->checkAccess('Customer') OR 
	         Yii::app()->user->checkAccess('Admin') OR
	         Yii::app()->user->isGuest )
	    {
	        return true;
	    }
	    return false; 
	}
	
	/**
	 * Узнать, может ли текущий пользователь редактировать анкету
	 * (только владелец анкеты или админ)
	 * 
	 * @param  int $userId - id пользователя редактируемой анкеты
	 * @return bool
	 * 
	 * @deprecated ииспользовать собственные роли RBAC
	 */
	protected function canEditUser($userId)
	{
	    if ( Yii::app()->user->checkAccess('Admin') OR Yii::app()->user->id == $userId )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Вывести кнопку для заказчика: пригласить или отозвать приглашение участника
	 * 
	 * @param  int    $id - id анкеты участника
	 * @param  string $type
	 * @return string
	 * 
	 * @deprecated вынести в виджет
	 */
	protected function createCustomerButton($id, $type)
	{
	    $messageId = 'order_message';
	    $buttonId  = 'order_button';
	    $questionary = Questionary::model()->findByPk($id);
	    // приглашение осуществляется через AJAX - так что подготовим настройки для него
	    $htmlOptions = array();
	    $htmlOptions['id'] = $buttonId;
	    
	    if ( $type == 'invite' )
	    {
	        $htmlOptions['class'] = 'btn btn-success btn-large';
	        $buttonCaption = QuestionaryModule::t('invite');
	        // JS для обработки успешного AJAX-запроса с приглашением
	        $successJS = $this->createInviteSuccessJS($messageId, $buttonId,$questionary->gender);
	        // url для приглашения
	        $buttonUrl = Yii::app()->createUrl('//questionary/questionary/invite', array('id' => $id));
	    }elseif ( $type == 'dismiss' )
	    {
	        $htmlOptions['class'] = 'btn btn-danger';
	        $buttonCaption = QuestionaryModule::t('dismiss');
	        // JS для обработки успешного AJAX-запроса с отменой приглашения
	        $successJS = $this->createDismissSuccessJS($messageId, $buttonId);
	        // url для отмены приглашения
	        $buttonUrl = Yii::app()->createUrl('//questionary/questionary/dismiss', array('id' => $id));
	    }else
	    {
	        throw CHttpException(404, 'Unknown button type');
	    }
	    
	    // JS для обработки ошибки AJAX
	    $errorJS = '';
	    
	    // @todo обработать случай ошибки AJAX
	    $ajaxOptions  = array(
    	    'url'      => $buttonUrl,
    	    'data'     => array(
    	        'id' => $id,
    	        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
	        'dataType' => 'json',
    	    //'type'     => 'post',
    	    //'error'  =>
            'success'  => $successJS,
        );
        // Сама кнопка приглашения/отмены
        return CHtml::ajaxButton($buttonCaption, $buttonUrl, $ajaxOptions, $htmlOptions);
	}
	
	/**
	 * Создать JS для отмены приглашения заказчиком
	 * 
	 * @param string $messageId - html-id тега с сообщением 
	 * @param string $buttonId - id кнопки с приглашением
	 * @return string
	 * 
	 * @deprecated вынести в виджет
	 */
	protected function createDismissSuccessJS($messageId, $buttonId)
	{
	    $afterDismissMessage = QuestionaryModule::t('dismiss_message');
	    $afterDismissCaption = QuestionaryModule::t('dismiss_message');
	    
	    return "function(data, status){
    	    if ( data.error.length )
    	    {
    	        
    	    }else
    	    {
        	    $('#{$messageId}').fadeIn(100);
        	    $('#{$messageId}').html('{$afterDismissMessage}');
        	    $('#{$messageId}').attr('class', 'alert alert-success');
        	     
        	    $('#{$buttonId}').attr('class', 'btn btn-primary disabled');
        	    $('#{$buttonId}').attr('disabled', 'disabled');
        	    $('#{$buttonId}').attr('value', '{$afterDismissCaption}');
    	    }
	    }";
	}
	
	/**
	 * Создать JS-код который приглашает участника на съемки
	 * 
	 * @param string $messageId - id тега с сообщением
	 * @param string $buttonId - id кнопки с отменой приглашения
	 * @param string $gender - пол участника (male/female)
	 * @return string
	 * 
	 * @deprecated вынести в виджет
	 */
	protected function createInviteSuccessJS($messageId, $buttonId, $gender)
	{
	    $afterInviteCaption = QuestionaryModule::t('already_invited(male)');
	    if ( $gender == 'female' )
	    {
	        $afterInviteCaption = QuestionaryModule::t('already_invited(female)');
	    }
	     
	    $myChoiceLink = CHtml::link(Yii::t('coreMessages','mainmenu_item_my_choice'),
	                        Yii::app()->createUrl('//catalog/catalog/myChoice'), array('target' => '_blank'));
	    $afterInviteMessage = QuestionaryModule::t('already_invited_message', array('{link}' => $myChoiceLink));
	    
	    return "function(data, status){
    	    if ( data.error.length )
    	    {
    	    	
    	    }else
    	    {
        	    $('#{$messageId}').fadeIn(100);
        	    $('#{$messageId}').html('{$afterInviteMessage}');
        	    $('#{$messageId}').attr('class', 'alert alert-success');
           
        	    $('#{$buttonId}').attr('class', 'btn btn-primary  btn-large disabled');
        	    $('#{$buttonId}').attr('disabled', 'disabled');
        	    $('#{$buttonId}').attr('value', '{$afterInviteCaption}');
    	    }
	    }";
	}
	
	/**
	 * Отправить письмо с подтверждением активации анкеты
	 * 
	 * @param  Questionary $questionary
	 * @param  string $password - пароль для входа на сайт
	 * @return null
	 * 
	 * @deprecated использовать модуль отправки писем
	 */
	protected function sendActivateConirmationEmail($questionary, $password)
	{
	    $message  = 'Ваша учетная запись активирована.<br>';
	    $message .= 'Ваш логин и пароль для входа на сайт:<br><br>';
	    $message .= 'Логин:'.$questionary->user->email.'<br>';
	    $message .= 'Пароль:'.$password.'<br><br>';
	    $message .= 'C уважением, команда проекта easyCast.';
	    
	    UserModule::sendMail($questionary->user->email, 'Учетная запись активирована', $message, true);
	}
}