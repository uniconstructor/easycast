<?php

/**
 * Административные действия с анкетой
 */
class QuestionaryController extends Controller
{
    public $layout='//layouts/column2';
    
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
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Questionary::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
    
    /**
     * Одобрить или отклонить анкету участника
     * @param int $id - id проверяенной анкеты
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
        
        $message = Yii::app()->request->getParam('message', '');
        if ( $status == 'active' )
        {// Подтверждение анкеты
            if ( $model->user->firstaccess )
            {// отправляем сообщение о том что анкета одобрена только в том случае если пользователь 
                // зарегистрировался не сам и еще ни разу не входил (чтобы не приходило 2 письма
                // при окончании заполнения анкеты администратором)
                $this->sendSuccessNotification($model, $message);
            }
        }elseif( $status == 'rejected' )
        {// отправка на доработку
            // Сообщаем пользователю по почте о том, что его анкета отправлена на доработку
            $this->sendRejectNotification($model, $message);
        }
        
	    echo 'OK';
    }
    
    /**
     * Отправить пользователю сообщение о том что его анкета одобрена
     * @param Questionary $questionary - анкета
     * @param string $message - сообщение от администратора
     */
    protected function sendSuccessNotification($questionary, $message=null)
    {
        $email   = $questionary->user->email;
        $subject = 'Ваша анкета одобрена';
        $text    = $this->getSuccessMessage($questionary, $message);
        
        UserModule::sendMail($email, $subject, $text);
    }
    
    /**
     * Отправить пользователю сообщение о том что его анкета отклонета (с комментарием)
     * @param unknown $questionary
     * @param string $message - сообщение от администратора
     */
    protected function sendRejectNotification($questionary, $message)
    {
        $email   = $questionary->user->email;
        $subject = 'В вашей анкете следует указать дополнительные данные';
        $text    = $this->getRejectMessage($questionary, $message);
        
        UserModule::sendMail($email, $subject, $text);
    }
    
    /**
     * Получить сообщение о том что анкета одобрена
     * @param Questionary $questionary - анкета
     * @param string $comment - сообщение от администратора
     * @return string
     */
    protected function getSuccessMessage($questionary, $comment=null)
    {
        $link = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $questionary->id));
        $message = 'Ваша анкета одобрена. Теперь она видна в поиске, и вы сможете получать приглашения на съемки. <br>
        Посмотреть анкету можно по ссылке: '.$link;
        
        if ( trim($comment) )
        {
            $message .= '<br><br>Комментарий администратора: '.$comment.'<br><br>';
        }
        $message .= 'После внесения дополнительных изменений ваша анкета станет доступна в поиске,
            и вы начнете получать приглашения на съемки.';
        
        return $message;
    }
    
    /**
     * Получить сообщение о том что анкета отклонена
     * @param Questionary $questionary - анкета
     * @param string $comment - сообщение от администратора
     * @return string
     */
    protected function getRejectMessage($questionary, $comment=null)
    {
        $link = Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $questionary->id));
        $message = 'Ваша анкета была проверена администратором и требует указания дополнительных данных.<br>
        <br>Вы можете просмотреть и отредактировать ее по ссылке: '.$link;
        
        $message .= '<br><br>Комментарий администратора: '.$comment;
        
        return $message;
    }
}