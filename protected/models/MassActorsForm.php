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
            array('birthdate', 'filter', 'filter' => array($this, 'checkBirthDate')),
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
     * Фильтр для даты рождения
     * @param int $galleryId
     * @return int
     */
    public function checkBirthDate($date)
    {
        return CDateTimeParser::parse($date, Yii::app()->params['inputDateFormat']);
    }
    
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
     * @return User
     * 
     * @todo обработать возможные ошибки
     */
    public function save()
    {
        // CVarDumper::dump($this, 10, true);
        // создаем пользователя
        $user = new User();
        $soucePassword  = $user->generatePassword();
        
        $user->email     = $this->email;
        $user->username  = $user->getLoginByEmail($user->email);
        $user->password  = UserModule::encrypting($soucePassword);
        $user->activkey  = UserModule::encrypting(microtime().$user->password);
        $user->superuser = 0;
        $user->status    = User::STATUS_ACTIVE;
        
        if ( ! $user->save() )
        {
            throw new CException('Не удалось создать пользователя');
        }
        
        // впускаем участника на сайт
        //$identity = new UserIdentity($user->username, $soucePassword);
        //$identity->authenticate();
        //Yii::app()->user->login($identity, 3600 * 24 * 30);
        
        if ( Yii::app()->getModule('user')->sendActivationMail )
        {// отправляем активационное письмо если нужно
            Yii::app()->getModule('user')->sendActivationEmail($user, $soucePassword);
        }
	    
        // заполняем анкету
        /* @var $questionary Questionary */
        $questionary = $user->questionary;
        $questionary->firstname   = $this->firstname;
        $questionary->lastname    = $this->lastname;
        $questionary->birthdate   = $this->birthdate;
        $questionary->mobilephone = $this->phone;
        $questionary->gender      = $this->gender;
        $questionary->ismassactor = 1;
        $questionary->status      = Questionary::STATUS_PENDING;
        // Устанавливаем и сохраняем условия съемок
        $questionary->recordingconditions->salary = 500;
        $questionary->recordingconditions->save();
        
        // заменяем галерею
        $oldGallery = $questionary->getGallery();
        $questionary->galleryid = $this->galleryid;
        $oldGallery->delete();
        // сохраняем анкету
        $questionary->save(false);
        
        return $user;
    }
}