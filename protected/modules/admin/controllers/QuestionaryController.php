<?php

/**
 * Административные действия с анкетой
 * @todo настроить права доступа
 */
class QuestionaryController extends Controller
{
    /**
     * @var string
     */
    public $layout = '//layouts/column2';
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     */
    protected $defaultModelClass = 'Questionary';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        // Импортируем классы для работы с анкетами пользователей
        Yii::import('application.modules.questionary.QuestionaryModule');
        Yii::import('application.modules.questionary.models.*');
        Yii::import('application.modules.questionary.models.complexValues.*');
        parent::init();
    }
    
    /**
     * Главная страница админки анкеты со списком всех возможных действий
     */
    public function actionIndex()
    {
        $this->render('index');
    }
    
    /**
     * Заявки на проверку анкеты
     */
    public function actionRequests()
    {
        $this->render('requests');
    }
    
    /**
     * Показать список отложенных анкет
     */
    public function actionDelayed()
    {
        $this->render('delayed');
    }
    
    /**
     * Показать введенные анкеты
     */
    public function actionCreated()
    {
        $id = Yii::app()->request->getParam('id', 0);
        $this->render('created', array('userId' => $id));
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * 
     * @param integer the ID of the model to be loaded
     */
    /*public function loadModel($id, $modelClass='Questionary')
    {
        $model = $modelClass::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $model;
    }*/
    
    /**
     * Одобрить или отправить на доработку анкету участника
     * 
     * @param  int $id - id проверяенной анкеты
     * @throws CHttpException
     * @return null
     */
    public function actionSetStatus($id)
    {
        $model = $this->loadModel($id);
        if ( ! $status = Yii::app()->request->getParam('status') )
        {
            throw new CHttpException(500, 'Необходимо указать статус');
        }
        if ( ! $model->setStatus($status) )
        {
            throw new CHttpException(500, 'Не удалось сменить статус');
        }
        // сообщение о смене статуса
        $message = Yii::app()->request->getParam('message', '');
        
        if ( $status === 'active' )
        {// Подтверждение анкеты
            if ( $model->user->firstaccess )
            {// отправляем сообщение о том что анкета одобрена только в том случае если пользователь 
                // зарегистрировался не сам и еще ни разу не входил (чтобы не приходило 2 письма
                // при окончании заполнения анкеты администратором)
                $this->sendSuccessNotification($model, $message);
            }
        }elseif( $status === 'rejected' )
        {// отправка на доработку
            // Сообщаем пользователю по почте о том, что его анкета отправлена на доработку
            $this->sendRejectNotification($model, $message);
        }
	    echo 'OK';
	    Yii::app()->end();
    }
    
    /**
     * Проверить анкету участника на соответствие критериям поиска роли и вывести результат в таблице 
     *
     * @return void
     * 
     * @todo принимать только POST AJAX запросы
     */
    public function actionForceCheck()
    {
        $vacancyId     = Yii::app()->request->getParam('vacancyId', 0);
        $questionaryId = Yii::app()->request->getParam('questionaryId', 0);
        // загружаем модели из базы
        /* @var $vacancy EventVacancy */
        $vacancy     = $this->loadModel($vacancyId, 'EventVacancy');
        /* @var $questionary Questionary */
        $questionary = $this->loadModel($questionaryId, 'Questionary');
        // выводим в таблице информацию о соответствии участника критериям поиска
        echo $this->widget('admin.extensions.SearchFilterCompare.SearchFilterCompare', array(
            'questionary' => $questionary,
            'vacancy'     => $vacancy,
        ), true);
        // выводим итоговый результат
        if ( $vacancy->isAvailableForUser($questionary->id) )
        {
            echo $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'success',
                'message' => 'Участник подходит по критериям',
            ), true);
        }else
        {
            echo $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'danger',
                'message' => 'Участник не подходит по критериям роли',
            ), true);
        }
        if ( $vacancy->hasApplication($questionary->id) )
        {
            echo $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'warning',
                'message' => 'Участник уже подал заявку на эту роль',
            ), true);
        }
        Yii::app()->end();
    }
    
    /**
     * Пригласить участника на роль, даже если он не подходит по критериям поиска
     *
     * @return void
     * 
     * @todo принимать только POST AJAX запросы
     */
    public function actionForceInvite()
    {
        $vacancyId     = Yii::app()->request->getParam('vacancyId', 0);
        $questionaryId = Yii::app()->request->getParam('questionaryId', 0);
        // загружаем модели из базы
        $vacancy     = $this->loadModel($vacancyId, 'EventVacancy');
        $questionary = $this->loadModel($questionaryId, 'Questionary');
        $invite      = EventInvite::model()->forEvent($vacancy->event)->
            forQuestionary($questionary)->find();
        if ( ! $invite )
        {// создаем приглашение - при сохранении модели
            // письмо с приглашением  будет составлено и отправлено участнику автоматически
            $invite = new EventInvite();
            $invite->questionaryid = $questionary->id;
            $invite->eventid       = $invite->eventid;
            $invite->save();
        }else
        {// используем существующее приглашение
            // составляем текст письма с приглашением
            $mailComposer = Yii::app()->getModule('mailComposer');
            $email   = $questionary->user->email;
            $subject = $mailComposer->getSubject('newInvite', array('invite' => $invite));
            $message = $mailComposer->getMessage('newInvite', array('invite' => $invite));
            // отправляем письмо с приглашением (вне очереди)
            Yii::app()->getComponent('ecawsapi')->sendMail($email, $subject, $message);
        }
        Yii::app()->end();
    }
    
    /**
     * Подать заявку от имени участника
     * 
     * @return void
     * 
     * @todo принимать только POST AJAX запросы
     */
    public function actionForceSubscribe()
    {
        $vacancyId     = Yii::app()->request->getParam('vacancyId', 0);
        $questionaryId = Yii::app()->request->getParam('questionaryId', 0);
        // загружаем модели из базы
        $vacancy     = $this->loadModel($vacancyId, 'EventVacancy');
        $questionary = $this->loadModel($questionaryId, 'Questionary');
        $member      = ProjectMember::model()->forQuestionary($questionary)->forVacancy($vacancy)->find();
        // поздаем заявку от имени участника
        if ( ! $member )
        {// заявки на эту роль от этого участника еще нет
            $member = new ProjectMember();
            $member->vacancyid = $vacancy->id;
            $member->memberid  = $questionary->id;
            // запоминаем администратора, подавшего заявку
            $member->managerid = Yii::app()->user->id;
            // @todo проверить результат сохранения
            $member->save();
        }
        Yii::app()->end();
    }
    
    /**
     * Отправить пользователю сообщение о том что его анкета одобрена
     * 
     * @param  Questionary $questionary - анкета
     * @param  string      $message - сообщение от администратора
     * @return void
     */
    protected function sendSuccessNotification($questionary, $message=null)
    {
        $email   = $questionary->user->email;
        $subject = 'Ваша анкета одобрена';
        $text    = $this->getSuccessMessage($questionary, $message);
        
        UserModule::sendMail($email, $subject, $text, true);
    }
    
    /**
     * Отправить пользователю сообщение о том что его анкета отклонета (с комментарием)
     * 
     * @param  Questionary $questionary
     * @param  string      $message - сообщение от администратора
     * @return void
     */
    protected function sendRejectNotification($questionary, $message)
    {
        $email   = $questionary->user->email;
        $subject = 'В вашей анкете следует указать дополнительные данные';
        $text    = $this->getRejectMessage($questionary, $message);
        
        UserModule::sendMail($email, $subject, $text, true);
    }
    
    /**
     * Получить сообщение о том что анкета одобрена
     * 
     * @param  Questionary $questionary - одобренная анкета
     * @param  string      $comment     - сообщение от администратора
     * @return string
     */
    protected function getSuccessMessage($questionary, $comment=null)
    {
        $link = Yii::app()->createAbsoluteUrl(Yii::app()->getModule('questionary')->profileUrl, array(
            'id' => $questionary->id,
        ));
        $message = 'Ваша анкета одобрена. Теперь при отборе заявок указанная вами информация будет 
            помечена как проверенная - такие анкеты чаще проходят этап предварительного отбора.<br>
            Посмотреть анкету можно по ссылке: '.$link;
        if ( trim($comment) )
        {
            $message .= '<br><br>Комментарий администратора: '.$comment.'<br><br>';
        }
        return $message;
    }
    
    /**
     * Получить сообщение о том что анкета отклонена
     * 
     * @param  Questionary $questionary - анкета
     * @param  string      $comment - сообщение от администратора
     * @return string
     * 
     * @todo token-ссылка для редактирования анкеты
     */
    protected function getRejectMessage($questionary, $comment=null)
    {
        $link     = Yii::app()->createAbsoluteUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $questionary->id));
        $message  = 'Ваша анкета была проверена администратором и требует указания дополнительных данных.<br>
            <br>Вы можете просмотреть и отредактировать ее по ссылке: '.$link.'.';
        $message .= '<br><br><i>(После внесения недостающей информации мы проверим вашу анкету еще раз -
            после этого указанная вами информация будет  помечена как проверенная - такие анкеты 
            чаще проходят этап предварительного отбора.)</i>';
        if ( trim($comment) )
        {
            $message .= '<br><br>Комментарий администратора: '.$comment;
        }
        return $message;
    }
}