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
        Yii::import('questionary.models.*');
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        return array(
            array('firstname, lastname, email, phone, birthdate, gender, galleryid', 'filter', 'filter' => 'trim'),
            // Сохраняем номер телефона в правильном формате (10 цифр)
            array('phone', 'LPNValidator', 'defaultCountry' => 'RU'),
            //array('galleryid', 'filter', 'filter' => array($this, 'checkPhotos')),
            array('email', 'email'),
            array('email', 'unique', /*'criteria' => array(),*/ 'className' => 'User'),
            // все поля формы обязательные
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
     * @see CModel::beforeValidate()
     */
    public function beforeValidate()
    {
        if ( ! $this->hasPhotos($this->galleryid) )
        {
            $this->addError('galleryid', 'Нужно загрузить хотя бы одну фотографию');
        }
        return parent::beforeValidate();
    }
    
    /**
     * Эта функция проверяет обязательное наличие хотя бы одной загруженной фотографии
     * @param int $galleryId
     * @return int
     */
    /*public function checkPhotos()
    {
        if ( ! $this->hasPhotos($this->galleryid) )
        {
            $this->addError('galleryid', 'Нужно загрузить хотя бы одну фотографию');
        }
        return $galleryId;
    }*/
    
    /**
     * Определить, загружена ли хотя бы одна фотография
     * @param int $galleryId
     * @return boolean
     * 
     * @todo сделать более надежную проверку безопасности при загрузке картинок
     */
    protected function hasPhotos($galleryId)
    {
        if ( ! $gallery = Gallery::model()->findByPk($galleryId) )
        {
            throw new CException('Ошибка при сохранении фотографий: невозможно найти галерею изображений');
        }
        if ( Questionary::model()->exists("`galleryid` = :galleryid", array(':galleryid' => $gallery->id)) )
        {// проверяем, что никто не подставил чужую галерею при сохранении
            throw new CException('Ошибка при сохранении фотографий: невозможно найти галерею изображений');
        }
        if ( $gallery->galleryPhotos )
        {// ни одной фотографии не загружено
            return true;
        }
        return false;
    }
    
    /**
     * Создать анкету для артиста массовых сцен
     * @return bool
     * @todo обработать возможные ошибки
     */
    public function save()
    {
        
    }
}