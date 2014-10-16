<?php

/**
 * Форма быстрой регистрации для массовки
 * 
 * @todo если не указать номер телефона - то выводится сообщение "phone cannot be blank"
 *       которое появляется непонятно откуда. Нужно понять почему это происходит и исправить
 */
class MassActorsForm extends CFormModel
{
    /**
     * @var string - имя
     */
    public $firstname;
    /**
     * @var string - фамилия
     */
    public $lastname;
    /**
     * @var string - email участника
     */
    public $email;
    /**
     * @var string - мобильный телефон
     */
    public $phone;
    /**
     * @var string - дата рождения 
     */
    public $birthdate;
    /**
     * @var string - пол (male/female)
     */
    public $gender;
    /**
     * @var int - id галереи изображений, которая создается при загрузке фотографий 
     *            (и только потом привязывается к созданному пользователю)
     */
    public $galleryid;
    /**
     * @var bool - согласие с условиями использования сайта
     */
    public $policyagreed;
    /**
     * @var int - размер оплаты за съемочный день
     */
    public $salary;
    
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
            array('firstname, lastname, email, phone, birthdate, gender, galleryid, salary', 'filter',
                'filter' => 'trim',
            ),
            // Сохраняем номер телефона в правильном формате (10 цифр)
            /*array('phone', 'LPNValidator', 
                'defaultCountry' => 'RU',
                'message'        => 'Неправильно указан номер телефона',
                'allowEmpty'     => false,
            ),*/
            // проверяем дату рождения отдельным фильтром
            array('birthdate', 'filter', 
                'filter'  => array($this, 'checkBirthDate'),
                'message' => 'Нужно указать дату рождения в формате дд.мм.гггг',
            ),
            // email должен быть уникальным при регистрации
            array('email', 'email'),
            array('email', 'unique', 'className' => 'User'),
            // галочка согласия с условиями обязательно должна стоять
            array('policyagreed', 'compare', 
                'allowEmpty'   => false,
                'compareValue' => 1,
                'message'      => 'Для регистрации требуется ваше согласие', 
            ),
            array('salary', 'numerical', 'integerOnly' => true),
            // все поля формы обязательные
            array('firstname, lastname, email, phone, birthdate, gender, galleryid, policyagreed', 'required'),
        );
    }
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        $policyUrl  = Yii::app()->createUrl('//site/page/view/license');
        $policyLink = CHtml::link('условиями использования', $policyUrl, array('target' => '_blank'));
        
        return array(
            'firstname'    => 'Имя',
            'lastname'     => 'Фамилия',
            'email'        => 'email',
            'phone'        => 'Мобильный телефон',
            'birthdate'    => 'Дата рождения',
            'gender'       => 'Пол',
            'galleryid'    => 'Фотографии',
            'policyagreed' => 'Соглашаюсь с '.$policyLink.' сайта',
            'salary'       => 'Присылать приглашения на съемки с оплатой от',
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
        {// фотографии есть
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
        $questionary->status      = Questionary::STATUS_PENDING;
        // Устанавливаем и сохраняем условия съемок
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