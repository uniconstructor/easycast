<?php

/**
 * Контроллер приглашений на мероприятия
 * Отвечает только за AJAX-запросы (принять, отклонить и т. п.)
 * Отображением приглашений занимаются различные виджеты
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
        return array(
            'accessControl', // perform access control
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
            array('allow',  // подписаться через токен можно и без регистрации
                'actions' => array('subscribe'),
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
        $this->setStatus(EventInvite::STATUS_ACCEPTED);
    }
    
    /**
     * Отклонить приглашение на участие в мероприятии (AJAX-запрос)
     * 
     * @return null
     */
    public function actionReject()
    {
        $this->setStatus(EventInvite::STATUS_REJECTED);
    }
    
    /**
     * Подать заявку на вакансию через токен приглашения
     * @todo залогинить пользователя сразу же как он зажел по токену (время сессии - час)
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
        if ( ! $invite = EventInvite::model()->findByPK($inviteId) )
        {
            throw new CHttpException(404, 'Страница не найдена');
        }
        if ( $key != $invite->subscribekey )
        {
            throw new CHttpException(404, 'Страница не найдена');
        }
        // ключ подошел - значит участник зашел по ссылке
        $this->render('subscribe', array('invite' => $invite));
    }
    
    /**
     * Подать заявку на участие в мероприятии (работает один раз)
     * @param Questionary $questionary
     * @param ProjectEvent $event
     * @throws CHttpException
     * @return bool
     * 
     * @todo писать какая именно вакансия закрыта
     * @todo подписывать на точно указанную вакансию, а не не первую попавшуюся
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
        // @todo 
        $vacancy = current($vacancies);
        // @todo
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
        $identity->setState('inviteLogin', true);
        $identity->authenticate();
        $identity->setState('inviteLogin', false);
        
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
     * Получить модель приглашения или сообщить о том что приглашение не найдено
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