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
     * (non-PHPdoc)
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        Yii::import('questionary.models.Questionary');
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
                'actions' => array('subscribe', 'selection', 'finishSelection'),
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
        if ( $id = Yii::app()->request->getParam('id', 0) )
        {
            $invite = $this->loadModel($id);
            $invite->setStatus(EventInvite::STATUS_ACCEPTED);
        }
    }
    
    /**
     * Отклонить приглашение на участие в мероприятии (AJAX-запрос)
     * 
     * @return null
     */
    public function actionReject()
    {
        $id = Yii::app()->request->getParam('id', 0);
        $invite = $this->loadModel($id);
        $invite->setStatus(EventInvite::STATUS_REJECTED);
    }
    
    /**
     * Подать заявку на вакансию через токен приглашения
     * 
     * @return null
     */
    public function actionSubscribe()
    {
        if ( ! $key = Yii::app()->request->getParam('key', '') )
        {
            throw new CHttpException(404, 'Страница не найдена');
        }
        
        $inviteId = Yii::app()->request->getParam('id', 0);
        $invite = $this->loadModel($inviteId);
        
        if ( $key != $invite->subscribekey )
        {
            throw new CHttpException(404, 'Страница не найдена');
        }
        // ключ подошел - значит участник зашел по ссылке. попробуем его залогинить.
        $this->quickLogin($invite->questionary);
        // @todo убрать if/else, и отображать оба случая одним виджетом, без ветвления
        if ( $invite->event->type == ProjectEvent::TYPE_GROUP )
        {// отобразить мероприятия и вакансии группы событый
            $this->render('subscribe', array('invite' => $invite));
        }else
        {// отобразить вакансии одного события 
            $this->render('tokenInvite', array('invite' => $invite, 'key' => $key));
        }
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
        
        // проверяем, что приглашение существует и ключи доступа правильные
        $customerInvite = $this->loadCustomerInviteModel($id);
        $this->checkCustomerInviteKeys($customerInvite, $key, $key2);
        
        // запоминаем, что приглашением воспользовались
        $customerInvite->markUsed();
                
        if ( Yii::app()->request->getParam('done') )
        {// нажата кнопка "завершить отбор"
            if ( $this->finishSelectionAllowed($customerInvite) )
            {// если заказчик закрыл все вакансии и ничего не забыл - перенаправляем его на
                // страницу завершения отбора
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
            echo $this->getInfoMessage('Вакансия была закрыта: набрано достаточное количество человек
                или истек срок подачи заявок.', 'Вакансия закрыта');
            return false;
        }
        
        $vacancy = current($vacancies);
        if ( $vacancy->status == EventVacancy::STATUS_FINISHED )
        {
            echo $this->getInfoMessage('Вакансия была закрыта: набрано достаточное количество человек 
                или истек срок подачи заявок.', 'Вакансия закрыта');
            return false;
        }
        if ( $vacancy->hasApplication($questionary->id) )
        {
            echo $this->getInfoMessage('Вы уже подали заявку на участие в этом мероприятии', 'Заявка уже подана');
            return false;
        }
        // все в порядке - создаем и сохраняем заявку
        $request = new MemberRequest();
        $request->vacancyid = $vacancy->id;
        $request->memberid  = $questionary->id;
        $request->save();
        
        return true;
    }
    
    /**
     * Залогинить пользователя по одноразовому ключу
     * @param Questionary $user
     * @return null
     * 
     * @todo рефакторинг: воспользоваться аналогичным методом из UserModule 
     *       Yii::app()->module('user')->forceLogin($user);
     * @deprecated 
     */
    protected function quickLogin($questionary)
    {
        if ( ! Yii::app()->user->isGuest )
        {
            return true;
        }
        if ( ! $questionary->user )
        {
            return false;
        }
                
        $identity = new UserIdentity($questionary->user->username, null);
        // хак с Identity для того чтобы залогинить пользователя по токену, не зная его пароля
        $identity->setState('inviteLogin', true);
        $identity->authenticate();
        $identity->clearState('inviteLogin');
        Yii::app()->user->login($identity, 0);
        
        return true;
    }
    
    /**
     * Изменить статус приглашения (можно только принять или отклонить, остальные статусы ставятся автоматически)
     * @param string $newStatus - новый статус приглашения
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
     * @param int $id
     * @throws CHttpException
     * @return EventInvite
     */
    protected function loadModel($id)
    {
        $model = EventInvite::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Приглашение не найдено');
        }
        return $model;
    }
    
    /**
     * Получить модель приглашения заказчика или сообщить о том что приглашение не найдено
     * @param int $id
     * @throws CHttpException
     * @return CustomerInvite
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
     * Определить, можно ли завершить отбор актеров (все ли вакансии закрыты?)
     * @param CustomerInvite $invite - приглашение заказчика
     * @return bool
     * 
     * @todo заглушка.
     */
    protected function finishSelectionAllowed($invite)
    {
        return true;
    }
    
    /**
     * Проверить ключи доступа из приглашения заказчика
     * @param CustomerInvite $invite - приглашение заказчика
     * @param string $key  - первый ключ безопасности (приходит из GET)
     * @param string $key2 - второй ключ безопасности (приходит из GET)
     * @throws CHttpException
     * @return null
     */
    protected function checkCustomerInviteKeys($invite, $key, $key2)
    {
        if ( $invite->key != $key OR $invite->key2 != $key2 )
        {// ключи доступа не совпадают
            throw new CHttpException(400, "Неправильная ссылка с приглашением ({$invite->id})");
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