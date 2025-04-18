<?php

/**
 * Контроллер приглашений на мероприятия (для участников) или на отбор актеров (для заказчиков)
 * Отвечает только за AJAX-запросы (принять, отклонить и т. п.) или вывод страниц
 * Отображением самих приглашений занимаются различные виджеты
 * 
 * @todo вынести все функции работы с приглашениями заказчика в отдельный контроллер
 * @todo создать новый модуль customer (кабинет заказчика) вынести контроллер приглашений заказчика туда 
 */
class InviteController extends Controller
{
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     */
    protected $defaultModelClass = 'EventInvite';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        Yii::import('questionary.models.Questionary');
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
	        array(
	           'ext.bootstrap.filters.BootstrapFilter + subscribe, selection, finishSelection',
            ),
	    );
	    return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     * 
     * @todo добавить работу с правами через RBAC
     */
    public function accessRules()
    {
        return array(
            array('allow',  // по одноразовой ссылке подписаться или отбирать актеров можно без авторизации
                'actions' => array('subscribe', 'selection', 'finishSelection', 'editMemberInstance'),
                'users'   => array('*'),
            ),
            array('allow',  // принять или отклонить приглашение на съемки могут только участники
                'actions' => array('accept', 'reject'),
                'users'   => array('@'),
            ),
            array('deny',  // для безопасности: запрещаем всё что явно не разрешено
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * Принять приглашение на участие в мероприятии (AJAX-запрос)
     * 
     * @return null
     * @todo сразу же добавлять заявку на вакансию, если она (вакансия) единственная
     */
    public function actionAccept()
    {
        $id     = Yii::app()->request->getParam('id', 0);
        $invite = $this->loadModel($id);
        $invite->setStatus(EventInvite::STATUS_ACCEPTED);
        Yii::app()->end();
    }
    
    /**
     * Отклонить приглашение на участие в мероприятии (AJAX-запрос)
     * 
     * @return null
     */
    public function actionReject()
    {
        $id     = Yii::app()->request->getParam('id', 0);
        $invite = $this->loadModel($id);
        $invite->setStatus(EventInvite::STATUS_REJECTED);
        Yii::app()->end();
    }
    
    /**
     * Подать заявку на вакансию через токен приглашения
     * 
     * @return null
     */
    public function actionSubscribe()
    {
        $inviteId = Yii::app()->request->getParam('id', 0);
        /* @var $invite EventInvite */
        $invite   = $this->loadModel($inviteId, 'EventInvite');
        if ( ! $key = Yii::app()->request->getParam('key', '') )
        {
            throw new CHttpException(404, 'Ссылка недействительна');
        }
        if ( ! $invite->validateKeys($inviteId, $key) )
        {// @todo предлагать отправить другую ссылку на это же действие
            throw new CHttpException(400, 'Ссылка недействительна');
        }
        // ключ подошел - значит участник зашел по ссылке. попробуем его залогинить.
        Yii::app()->getModule('user')->forceLogin($invite->questionary->user);
        if ( $invite->event->project->id == 403 )
        {// сбор заявок для проектов, регистрация на которые проходит только на официальном сайте
            // (проект "Шоу Я": функционал требовался перед окончательным переходом сайта на вторую версию)
            $externalUrl  = 'http://xn----0tbps2b.xn--p1ai/?utm_source=easycast_ru&utm_medium=cpc&utm_campaign=easycast_ru';
            $this->render('/vacancy/external', array(
                'externalUrl' => $externalUrl,
                'project'     => $invite->event->project,
            ));
            return;
        }
        $this->render('tokenInvite', array('invite' => $invite, 'key' => $key));
    }
    
    /**
     * Отобразить страницу отбора актеров для заказчика
     * 
     * @return null
     * 
     * @todo сделать защиту от повторного использования и проверку по времени
     * @todo проверять статус связанного объекта и выводить сообщение, если он уже завершен
     * @todo заходить под учетной записью заказчика, если она у него есть
     * @todo проверять статус приглашения
     * @todo проверять, можно ли завершить отбор
     */
    public function actionSelection()
    {
        // id приглашения заказчика
        $id   = Yii::app()->request->getParam('id', 0);
        // одноразовые ключи безопасности
        $key  = Yii::app()->request->getParam('k1', '');
        $key2 = Yii::app()->request->getParam('k2', '');
        
        // отключаем счетчики и аналитику в процессе отбора чтобы не дать поисковикам 
        // случайно проиндексировать пользовательские данные со всеми контактами
        $this->analytics = false;
        
        // проверяем, что приглашение существует и ключи доступа правильные
        $customerInvite = $this->loadCustomerInviteModel($id);
        $this->checkCustomerInviteKeys($customerInvite, $key, $key2);
        // запоминаем, что приглашением воспользовались
        $customerInvite->markUsed();
                
        if ( Yii::app()->request->getParam('done') )
        {// нажата кнопка "завершить отбор"
            if ( $this->finishSelectionAllowed($customerInvite) )
            {// если заказчик закрыл все вакансии и ничего не забыл - то
                // перенаправляем его на страницу завершения отбора
                $this->redirect(Yii::app()->createUrl('/projects/invite/finishSelection', array(
                    'id' => $customerInvite->id,
                    'k1' => $customerInvite->key,
                    'k2' => $customerInvite->key2,
                )));
            }else
            {// @todo отображаем сообщение о том, что отбор еще не закончен
                
            }
        }
        // отображаем страницу со списком участников для отбора
        $this->render('selection', array('customerInvite' => $customerInvite));
    }
    
    /**
     * Отобразить страницу с сообщением о том, что отбор участников окончен
     * и предложением перейти к проекту
     * @return null
     * 
     * @todo отсылать письмо команде при завершении отбора заказчиком
     */
    public function actionFinishSelection()
    {
        // id приглашения заказчика
        $id   = Yii::app()->request->getParam('id', 0);
        // одноразовые ключи безопасности
        $key  = Yii::app()->request->getParam('k1', '');
        $key2 = Yii::app()->request->getParam('k2', '');
        
        // проверяем, что приглашение существует и ключи доступа правильные
        $customerInvite = $this->loadCustomerInviteModel($id);
        $this->checkCustomerInviteKeys($customerInvite, $key, $key2);
        
        // помечаем приглашение использованным
        $customerInvite->setStatus(CustomerInvite::STATUS_FINISHED);
        
        $this->render('finishSelection');
    }
    
    /**
     * Редактировать список разделов по одноразовой ссылке
     * @return void
     * 
     * @todo проверять что редактируемая заявка принадлежит роли в приглашении
     */
    public function actionEditMemberInstance()
    {
        // id приглашения заказчика
        $id   = Yii::app()->request->getParam('ciid', 0);
        // одноразовые ключи безопасности
        $key  = Yii::app()->request->getParam('k1', '');
        $key2 = Yii::app()->request->getParam('k2', '');
        
        // проверяем, что приглашение существует и ключи доступа правильные
        $customerInvite = $this->loadModel($id, 'CustomerInvite');
        $this->checkCustomerInviteKeys($customerInvite, $key, $key2);
        
        // получаем все параметры для редактирования одной записи
        $memberId = Yii::app()->request->getParam('pk');
        $field    = Yii::app()->request->getParam('name');
        $value    = Yii::app()->request->getParam('value');
        // а также саму запись
        $item             = MemberInstance::model()->findByPk($memberId);
        $item->$field     = $value;
        $item->sourcetype = 'customer_invite';
        $item->sourceid   = $customerInvite->id;
        // перед сохранением запоминаем старый статус
        $oldStatus        = $item->status;
        
        if ( ! $item->save() )
        {// не удалось обновить запись в поле
            throw new CHttpException(500, $item->getError($field));
        }else
        {// запоминаем кто последний редактировал запись
            $newStatus = $item->status;
            // @todo переписать с использованием событий
            $history = new StatusHistory();
            $history->sourcetype = 'customer_invite';
            $history->sourceid   = $customerInvite->id;
            $history->objecttype = 'member_instance';
            $history->objectid   = $memberId;
            $history->oldstatus  = $oldStatus;
            $history->newstatus  = $newStatus;
            $history->save();
            echo 'OK';
        }
        Yii::app()->end();
    }

    /**
     * Подать заявку на участие в мероприятии если есть только одна доступная вакансия
     * @param Questionary $questionary
     * @param ProjectEvent $event
     * @throws CHttpException
     * @return bool
     * 
     * @todo писать какая именно вакансия закрыта
     * @todo подписывать на точно указанную вакансию, а не не первую попавшуюся
     * @todo логика работы с приглашениями изменилась: удалить при рефакторинге, если не пригодится
     */
    protected function createMemberRequest($questionary, $event)
    {
        if ( ! $questionary OR ! $event )
        {// @todo проработать эту ошибку внимательнее
            throw new CHttpException(404, 'Страница не найдена');
        }
        if ( ! $vacancies = $event->activevacancies )
        {
            echo $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'info',
                'header'  => 'Набор завершен',
                'message' => 'Роль закрыта: набрано достаточное количество человек
                    или истек срок подачи заявок.',
            ), true);
            return false;
        }
        $vacancy = current($vacancies);
        if ( $vacancy->status == EventVacancy::STATUS_FINISHED )
        {
            echo $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'info',
                'header'  => 'Набор завершен',
                'message' => 'Роль закрыта: набрано достаточное количество человек 
                    или истек срок подачи заявок.',
            ), true);
            return false;
        }
        if ( $vacancy->hasApplication($questionary->id) )
        {
            echo $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'info',
                'header'  => 'Заявка принята',
                'message' => 'Вы уже подали заявку на участие в этом мероприятии.',
            ), true);
            return false;
        }
        // все в порядке - создаем и сохраняем заявку
        $request = new MemberRequest();
        $request->vacancyid = $vacancy->id;
        $request->memberid  = $questionary->id;
        return $request->save();
    }
    
    /**
     * Залогинить пользователя по одноразовому ключу
     * 
     * @param  Questionary $user
     * @return null
     * 
     * @deprecated рефакторинг: воспользоваться аналогичным методом из UserModule 
     *       Yii::app()->module('user')->forceLogin($user);
     */
    protected function quickLogin($questionary)
    {
        Yii::app()->getModule('user')->forceLogin($questionary->user);
    }
    
    /**
     * Изменить статус приглашения (можно только принять или отклонить, остальные статусы ставятся автоматически)
     * @param  string $newStatus - новый статус приглашения
     * @return bool
     * 
     * @todo обработать возможные ошибки
     */
    protected function setStatus($newStatus)
    {
        $inviteId = Yii::app()->request->getParam('id', 0);
        $invite   = $this->loadModel($inviteId);
        
        if ( Yii::app()->user->id == $invite->questionary->user->id )
        {// принимать можно только свои приглашения
            $invite->setStatus($newStatus);
            echo 'OK';
        }
        Yii::app()->end();
    }
    
    /**
     * Получить модель приглашения участника или сообщить о том что приглашение не найдено
     * 
     * @param  int $id
     * @return EventInvite
     * @throws CHttpException
     */
    /*protected function loadModel($id)
    {
        $model = EventInvite::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Приглашение не найдено');
        }
        return $model;
    }*/
    
    /**
     * Получить модель приглашения заказчика или сообщить о том что приглашение не найдено
     * 
     * @param  int $id
     * @return CustomerInvite
     * @throws CHttpException
     * 
     * @deprecated использовать обновленный $this->loadModel($id, $class);
     */
    protected function loadCustomerInviteModel($id)
    {
        $model = CustomerInvite::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Приглашение не найдено');
        }
        return $model;
    }
    
    /**
     * Определить, можно ли завершить отбор актеров (все ли роли закрыты?)
     * 
     * @param  CustomerInvite $invite - приглашение заказчика
     * @return bool
     * 
     * @todo пока что только заглушка
     */
    protected function finishSelectionAllowed($invite)
    {
        return true;
    }
    
    /**
     * Проверить ключи доступа из приглашения заказчика
     * 
     * @param  CustomerInvite $invite - приглашение заказчика
     * @param  string $key  - первый ключ безопасности (приходит из GET)
     * @param  string $key2 - второй ключ безопасности (приходит из GET)
     * @return null
     * @throws CHttpException
     */
    protected function checkCustomerInviteKeys($invite, $key, $key2)
    {
        if ( ! $invite->checkKeys($invite->id, $key, $key2) )
        {// ключи доступа не совпадают
            throw new CHttpException(400, "Документ устарел");
        }
    }
    
    /**
     * 
     * @param unknown $message
     * @param string $header
     * @param string $class
     * @return string
     * 
     * @todo заменить виджетом
     * @deprecated виджет создан (ECAlert) - заменить все вызовы и удалить эту функцию при рефакторинге
     */
    protected function getInfoMessage($message, $header='', $class='alert alert-block')
    {
        $result = '';
        
        $result .= '<div class="'.$class.'" style="text-align:center;">';
        if ( $header )
        {
            $header = '<h4 class="alert-heading">'.$header.'</h4>';
            $result .= $header;
        }
        $result .= $message.'</div>';
        
        return $result;
    }
}