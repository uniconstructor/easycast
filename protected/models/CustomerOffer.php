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
        // отправляем письмо (сразу же, без очереди)
    
        return true;
    }
}