<?php

/**
 * Форма для добавления нового участника в фотовызывной вручную
 * Используется для того чтобы добавить в уже сформированный фотовызывной новых участников, 
 * которые отсутствуют в нашей базе
 * @deprecated мы отказались от этой задачи. Сейчас класс нигде не используется, удалить при рефакторинге
 */
class ExternalMemberForm extends CFormModel
{
    public $firstname;
    public $lastname;
    public $age;
    public $phone;
    public $bages;
    public $comment;
    public $galleryid;
    
    public $reportid;
    public $vacancyid;
    public $hash;
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        Yii::import('ext.galleryManager.*');
        Yii::import('ext.galleryManager.models.*');
    
        Yii::import('ext.LPNValidator.LPNValidator');
        
        parent::init();
    }
    
    /**
     * Эта форма только для администраторов, поэтому обязательных полей нет
     * @see CModel::rules()
     */
    public function rules()
    {
        return array(
            array('firstname, lastname, age, phone, bages, comment, 
                galleryid, vacancyid, reportid, hash', 'filter', 'filter' => 'trim'),
            // @todo сохраняем номер телефона в правильном формате (10 цифр)
            //array('phone', 'LPNValidator', 'defaultCountry' => 'RU'),
        );
    }
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return array(
            'firstname' => 'Имя',
            'lastname'  => 'Фамилия',
            'phone'     => 'Телефон',
            'age'       => 'Возраст',
            'bages'     => 'Характеристики',
            'comment'   => 'Комментарий',
            'galleryid' => 'Фото',
        );
    }
}