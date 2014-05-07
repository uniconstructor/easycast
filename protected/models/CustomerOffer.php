<?php

/**
 * Модель для работы с коммерческим предложением, отправляемым с сайта
 * @todo прикреплять коммерческое предложение к созданой учетной записи заказчика (если она была создана)
 */
class CustomerOffer extends CustomerInvite
{
    /**
     * @param system $className
     * @return CustomerOffer
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    /**
     * @see CustomerInvite::beforeSave()
     */
    public function beforeSave()
    {
        if ( $this->isNewRecord )
        {// коммерческое предложение пока не прикрепляется ни к какому объекту в системе
            $this->objecttype = 'offer';
            $this->objectid   = 0;
        }
        return parent::beforeSave();
    }
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'condition' => "`objecttype`='offer'",
            'order'     => '`timecreated` DESC',
        );
    }
    
    /**
     * @see CustomerInvite::sendNotification()
     */
    public function sendNotification()
    {
        $params = array(
            'offer'   => $this,
            'manager' => $this->manager,
        );
        // отправляем письмо (сразу же, без очереди)
        $message   = Yii::app()->getModule('mailComposer')->getMessage('offer', $params);
        $subject   = 'Коммерческое предложение кастингового агентства easyCast.ru (КАСТИНГИ, АКТЕРЫ, ГРУППОВКА, МАССОВКА, МОДЕЛИ и всевозможные КОЛЛЕКТИВЫ для ваших проектов!)';
        $userEmail = Yii::app()->getModule('user')->user()->email;
        $fullName  = Yii::app()->getModule('user')->user()->fullname;
        
        if ( Yii::app()->user->id == 1 OR ! in_array($userEmail, Yii::app()->params['verifiedSenders']) )
        {// если email не в списке проверенных адресов - отправляем письмо от имени руководителя
            $from = 'ceo@easycast.ru';
        }else
        {
            $from = $userEmail;
        }
        
        Yii::app()->getModule('user')->sendMail($this->email, $subject, $message, true, $from);
        return true;
    }
    
    /**
     * @see CustomerInvite::markUsed()
     * @todo проверить что предыдущая смена статуса завершилась успешно
     */
    public function markUsed()
    {
        if ( $this->status === self::STATUS_FINISHED )
        {// приглашение уже использовано
            return true;
        }
        if ( Yii::app()->user->checkAccess('Admin') )
        {// админ просто проверяет приглашение перед отправкой
            return true;
        }
        
        parent::markUsed();
        $this->timefinished = time();
        return $this->setStatus(self::STATUS_FINISHED);
    }
}