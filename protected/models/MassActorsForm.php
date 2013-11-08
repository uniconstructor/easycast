<?php

/**
 * Форма быстрой регистрации для массовки
 * 
 * @todo документировать все поля
 */
class MassActorsForm extends CFormModel
{
    public $firstname; 
    public $lastname; 
    public $email; 
    public $phone;
    public $birthdate; 
    public $gender; 
    public $galleryid;
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        Yii::import('ext.galleryManager.*');
        Yii::import('ext.galleryManager.models.*');
        
        Yii::import('ext.LPNValidator.LPNValidator');
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        return array(
            array('firstname, lastname, email, phone, birthdate, gender, galleryid', 'filter', 'filter' => 'trim'),
            //array('phone', 'filter', 'filter' => array($this, 'filterPhone')),
            array('phone', 'LPNValidator', 'defaultCountry' => 'RU'),
            array('email', 'email'),
            array('email', 'unique', /*'criteria' => array(),*/ 'className' => 'User'),
            // все поля обязательные
            array('firstname, lastname, email, phone, birthdate, gender, galleryid', 'required'),
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
            'email'     => 'email',
            'phone'     => 'Мобильный телефон',
            'birthdate' => 'Дата рождения',
            'gender'    => 'Пол',
            'galleryid' => 'Фотографии',
        );
    }
    
    /**
     * Эта функция проверяет обязательное наличие хотя бы одной загруженной фотографии
     * @see CModel::afterValidate()
     */
    protected function afterValidate()
    {
        /*if ( ! $this->hasPhotos() )
        {
            $this->addError('galleryid', 'Нужно загрузить хотя бы одну фотографию');
        }*/
        parent::afterValidate();
    }
    
    /**
     * Определить, загружена ли хотя бы одна фотография
     * @return boolean
     */
    protected function hasPhotos()
    {
        return false;
    }
    
    /**
     * 
     * @param unknown $phone
     * @return void
     */
    public function filterPhone($phone)
    {
        $validator = new LPNValidator();
        $validator->defaultCountry = 'RU';
        return $validator->validateAttribute($this, 'phone');
    }
}