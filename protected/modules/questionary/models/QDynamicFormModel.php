<?php

/**
 * Модель для динамической формы анкеты
 * Определяет нужные проверки (rules) в зависимости от того какие поля нужно вывести
 * 
 * @todo определить сценарии формы
 */
class QDynamicFormModel extends CFormModel
{
    // поля модели User
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $policyagreed;
    // поля модели Questionary
    /**
     * @var string
     */
    public $firstname;
    /**
     * @var string
     */
    public $lastname;
    /**
     * @var string
     */
    public $middlename;
    /**
     * @var string
     */
    public $birthdate;
    /**
     * @var string
     * @todo
     */
    //public $playagemin;
    /**
     * @var string
     * @todo
     */
    //public $playagemax;
    /**
     * @var string
     */
    public $gender;
    /**
     * @var string
     */
    public $height;
    /**
     * @var string
     */
    public $weight;
    /**
     * @var string
     */
    public $wearsize;
    /**
     * @var string
     */
    public $shoessize;
    /**
     * @var string
     */
    public $nativecountryid;
    /**
     * @var string
     */
    public $currentcountryid;
    /**
     * @var string
     */
    public $countryid;
    /**
     * @var string
     */
    public $cityid;
    /**
     * @var string
     */
    public $city;
    /**
     * @var string
     */
    public $mobilephone;
    /**
     * @var string
     */
    public $homephone;
    /**
     * @var string
     */
    public $addphone;
    /**
     * @var string
     */
    public $vkprofile;
    /**
     * @var string
     */
    public $fbprofile;
    /**
     * @var string
     */
    public $okprofile;
    /**
     * @var string
     */
    public $haircolor;
    /**
     * @var string
     */
    public $hairlength;
    /**
     * @var string
     */
    public $eyecolor;
    /**
     * @var string
     */
    public $physiquetype;
    /**
     * @var string
     */
    public $chestsize;
    /**
     * @var string
     */
    public $waistsize;
    /**
     * @var string
     */
    public $hipsize;
    /**
     * @var string
     */
    public $titsize;
    /**
     * @var string
     * @todo
     */
    //public $hastatoo;
    /**
     * @var string
     */
    public $isactor;
    /**
     * @var string
     */
    public $isamateuractor;
    /**
     * @var string
     */
    public $hasfilms;
    /**
     * @var string
     */
    public $istheatreactor;
    /**
     * @var string
     */
    public $isstatist;
    /**
     * @var string
     */
    public $ismassactor;
    /**
     * @var string
     */
    public $isemcee;
    /**
     * @var string
     */
    public $istvshowmen;
    /**
     * @var string
     */
    public $isparodist;
    /**
     * @var string
     */
    public $istwin;
    /**
     * @var string
     */
    public $ismodel;
    /**
     * @var string
     */
    public $isphotomodel;
    /**
     * @var string
     */
    public $ispromomodel;
    /**
     * @var string
     */
    public $isdancer;
    /**
     * @var string
     */
    public $isstripper;
    /**
     * @var string
     */
    public $issinger;
    /**
     * @var string
     */
    public $ismusician;
    /**
     * @var string
     */
    public $issportsman;
    /**
     * @var string
     */
    public $isextremal;
    /**
     * @var string
     */
    public $isathlete;
    /**
     * @var string
     */
    public $hasskills;
    /**
     * @var string
     */
    public $hastricks;
    /**
     * @var string
     */
    public $haslanuages;
    /**
     * @var string
     */
    public $hasawards;
    /**
     * @var string
     */
    public $galleryid;
    /**
     * @var Gallery
     */
    public $gallery;
    /**
     * @var string
     * @deprecated
     */
    public $photo;
    /**
     * @var string
     * @todo
     */
    //public $rating;
    /**
     * @var string
     * @todo
     */
    //public $privatecomment;
    // поля модели QRecordingConditions
    /**
     * @var string
     */
    public $isnightrecording;
    /**
     * @var string
     */
    public $istoplessrecording;
    /**
     * @var string
     */
    public $isfreerecording;
    /**
     * @var string
     */
    public $wantsbusinesstrips;
    /**
     * @var string
     */
    public $hasforeignpassport;
    /**
     * @var string
     */
    public $passportexpires;
    /**
     * @var string
     */
    public $salary;
    /**
     * @var string префикс для имен всех дополнительных полей формы
     */
    public $extraFieldPrefix = 'ext_';
    
    /**
     * @var Questionary - редактируемая или создаваемая (при регистрации) анкета
     */
    protected $questionary;
    /**
     * @var EventVacancy - роль, на которую подается заявка при регистрации через эту форму
     */
    protected $vacancy;
    /**
     * @var QUserField[] - список не заполненных полей анкеты, которые нужно указать прежде чем подать заявку на роль
     */
    protected $emptyUserFields = array();
    /**
     * @var QUserField[] - список всех полей анкеты, которые нужно указать прежде чем подать заявку на роль
     */
    protected $userFields = array();
    /**
     * @var ExtraField[] - список не заполненных дополнительных полей, 
     *                     которые нужно указать прежде чем подать заявку на роль
     *                     (не хранятся в анкете, потому что как правило требуются один раз для одной роли)
     */
    protected $emptyExtraFields = array();
    /**
     * @var ExtraField[] - список всех дополнительных полей анкеты, 
     *                     которые нужно указать прежде чем подать заявку на роль
     *                     (не хранятся в анкете, потому что как правило требуются один раз для одной роли)
     */
    protected $extraFields = array();
    /**
     * @var array - список правил формы для метода rules()
     *              (составляется в зависимости от требований роли) 
     */
    protected $_rules = array();
    /**
     * @var array - массив с доп. полями анкеты. Нужен чтобы форма воспринимала из как свои родные поля
     */
    protected $_attributes = array();
    
    /**
     * PHP getter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @return mixed property value
     * @see getAttribute
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch ( Exception $e )
        {
            if ( isset($this->_attributes[$name]) )
            {
                return $this->_attributes[$name];
            }elseif ( property_exists($this, $name) )
            {
                return $this->$name;
            }
        }
    }
    
    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name,$value)
    {
        try {
            parent::__set($name,$value);
        }catch ( Exception $e )
        {
            $this->setAttribute($name, $value);
        }
    }
    
    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking
     * if the named attribute is null or not.
     * @param string $name the property name or the event name
     * @return boolean whether the property value is null
     */
    public function __isset($name)
    {
        if ( parent::__isset($name) )
        {
            return true;
        }elseif ( isset($this->_attributes[$name]) )
        {
            return true;
        }else
        {
            return false;
        }   
    }
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        parent::init();
        
        Yii::import('ext.LPNValidator.LPNValidator');
    }
    
    /**
     * @param EventVacancy $vacancy
     * @return void
     */
    public function setVacancy(EventVacancy $vacancy)
    {
        if ( $vacancy->isNewRecord )
        {
            throw new CException('Невозможно получить список правил для формы: роль еще не создана');
        }
        $this->vacancy = $vacancy;
        // определяем список необходимых для заполнения полей и проверок в зависимости от анкеты и роли
        $this->setUpFieldList();
        $this->setUpRules();
        $this->setUpData();
    }
    
    /**
     * @param Questionary $questionary
     * @return void
     */
    public function setQuestionary(Questionary $questionary)
    {
        $this->questionary = $questionary;
        // определяем список необходимых для заполнения полей и проверок в зависимости от анкеты и роли
        $this->setUpFieldList();
        $this->setUpRules();
        $this->setUpData();
    }
    
    /**
     * @param Questionary $questionary
     * @return void
     */
    public function setGallery(Gallery $gallery)
    {
        $this->questionary->gallery = $gallery->id;
    }
    
    /**
     * @return EventVacancy
     */
    public function getVacancy()
    {
        return $this->vacancy;
    }
    
    /**
     * @return Questionary
     */
    public function getQuestionary()
    {
        return $this->questionary;
    }
    
    /**
     * Получить только незаполненные поля анкеты, которые требуются ролью для подачи заявки
     * @return QUserField[] 
     */
    public function getEmptyUserFields()
    {
        return $this->emptyUserFields;
    }
    
    /**
     * Получить все поля анкеты, которые требуются ролью для подачи заявки
     * @return QUserField[]
     */
    public function getUserFields()
    {
        return $this->userFields;
    }
    
    /**
     * Получить только незаполненные дополнительные поля, которые требуются ролью для подачи заявки,
     * но не содержатся в анкете (потому что требуются только 1 раз на роль)
     * @return ExtraField[] 
     */
    public function getEmptyExtraFields()
    {
        return $this->emptyExtraFields;
    }
    
    /**
     * Получить все дополнительные поля, которые требуются ролью для подачи заявки,
     * но не содержатся в анкете (потому что требуются только 1 раз на роль)
     * @return ExtraField[]
     */
    public function getExtraFields()
    {
        return $this->extraFields;
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        return $this->_rules;
    }
    
    /**
     * Создать список всех требуемых полей формы и список незаполненых полей
     * @return void
     */
    protected function setUpFieldList()
    {
        if ( ! $this->questionary OR ! $this->vacancy )
        {// можем начинать только если есть все необходимые параметры
            return;
        }
        
        foreach ( $this->vacancy->userFields as $userField )
        {/* @var $userField QUserField */
            if ( ! $this->questionary->id OR $userField->isEmptyIn($this->questionary) )
            {// определяем каких полей для этой роли не хватает в анкете и храним их отдельно
                // это нужно для того чтобы была возможность отобразить форму только с необходимым
                // минимумом полей (для ранее созданных анкет, при подаче заявки)
                $this->emptyUserFields[$userField->name] = $userField;
            }
            // параллельно составляем полный список требуемых полей
            // (если потребуется дать возможность дополнять информацию перед подачей заявки)
            $this->userFields[$userField->name] = $userField;
        }
        foreach ( $this->vacancy->extraFields as $extraField )
        {/* @var $extraField ExtraField */
            if ( ! $this->questionary->id OR $extraField->isEmptyForVacancy($this->vacancy, $this->questionary) )
            {// определяем каких полей для этой роли не хватает в заявке и храним их отдельно
                // это нужно для того чтобы была возможность отобразить форму только с необходимым
                // минимумом полей 
                // (для для тех кто уже подал заявку, но получил сообщение о необходимости дополнить информацию)
                $this->emptyExtraFields[$extraField->name] = $extraField;
            }
            // параллельно составляем полный список дополнительных полей
            $this->extraFields[$extraField->name] = $extraField;
            // и также записываем эти же поля в модель формы, чтобы форма думала что это ее родные поля
            $this->_attributes[$this->extraFieldPrefix.$extraField->name] = null;
        }
    }
    
    /**
     * Создать набор правил для формы в зависимости от того какие поля необходимо 
     * заполнить для регистрации на роль
     * @return void
     */
    protected function setUpRules()
    {
        if ( ! $this->questionary OR ! $this->vacancy )
        {// можем начинать только если есть все необходимые параметры
            return;
        }
        
        foreach ( $this->userFields as $userField )
        {// прибавляем правила проверки полей для формы анкеты
            $this->_rules = CMap::mergeArray($this->_rules, $this->getUserFieldRules($userField));
        }
        foreach ( $this->extraFields as $extraField )
        {/// добавляем правила для дополнительных полей
            $this->_rules = CMap::mergeArray($this->_rules, $this->getExtraFieldRules($extraField));
        }
    }
    
    /**
     * Получить набор правил для поля формы
     * @param QUserField - поле, для которого нужно получить правила формы
     * @return array
     */
    protected function getUserFieldRules(QUserField $field)
    {
        $rules = array(
            'vkprofile'   => array(array('vkprofile', 'length', 'max' => 255)),
            'fbprofile'   => array(array('fbprofile', 'length', 'max' => 255)),
            'okprofile'   => array(array('okprofile', 'length', 'max' => 255)),
            'firstname'   => array(array('firstname', 'length', 'max' => 128)),
            'lastname'    => array(array('lastname', 'length', 'max' => 128)),
            'middlename'  => array(array('middlename', 'length', 'max' => 128)),
            'city'        => array(array('city', 'length', 'max' => 128)),
            'mobilephone' => array(
                array('mobilephone', 'length', 'max' => 32),
                array('mobilephone', 'LPNValidator',
                    'defaultCountry' => 'RU',
                    'message'        => 'Неправильно указан номер телефона',
                    'emptyMessage'   => 'Не указан номер телефона',
                    //'allowEmpty'     => false,
                ),
            ),
            'homephone' => array(
                array('homephone', 'length', 'max' => 32),
                array('homephone', 'LPNValidator',
                    'defaultCountry' => 'RU',
                    'message'        => 'Неправильно указан номер телефона',
                    'emptyMessage'   => 'Не указан номер телефона',
                    //'allowEmpty'     => false,
                ),
                
            ),
            'addphone' => array(
                array('addphone', 'length', 'max' => 32),
                array('addphone', 'LPNValidator',
                    'defaultCountry' => 'RU',
                    'message'        => 'Неправильно указан номер телефона',
                    'emptyMessage'   => 'Не указан номер телефона',
                    //'allowEmpty'     => false,
                ),
            ),
            'email' => array(
                array('email', 'email'),
            ),
            'policyagreed' => array(
                array('policyagreed', 'compare',
                    'compareValue' => 1,
                    'message'      => 'Для регистрации требуется ваше согласие',
                    //'allowEmpty'   => false,
                ),
            ),
            'birthdate' => array(
                array('birthdate', 'filter',
                    'filter'  => array($this, 'checkInputDate'),
                    'message' => 'Нужно указать дату в формате дд.мм.гггг',
                ),
            ),
            'passportexpires' => array(
                array('passportexpires', 'filter',
                    'filter'  => array($this, 'checkInputDate'),
                    'message' => 'Нужно указать дату в формате дд.мм.гггг',
                ),
            ),
            'photo' => array(array('photo', 'safe')),
            'chestsize' => array(
                array('chestsize', 'compare', 'compareValue' => 1, 'operator' => '>', 
                    'message' => 'Не указаны параметры тела (объем груди)',
                ),
            ),
            'waistsize' => array(
                array('waistsize', 'compare', 'compareValue' => 1, 'operator' => '>',
                    'message' => 'Не указаны модельные параметры тела (объем талии)',
                ),
            ),
            'hipsize' => array(
                array('hipsize', 'compare', 'compareValue' => 1, 'operator' => '>',
                    'message' => 'Не указаны модельные параметры тела (объем бедер)',
                ),
            ),
            'height' => array(
                array('height', 'compare', 'compareValue' => 1, 'operator' => '>',
                    'message' => 'Не указан рост',
                ),
            ),
            'weight' => array(
                array('weight', 'compare', 'compareValue' => 1, 'operator' => '>',
                    'message' => 'Не указан вес',
                ),
            ), 
        );
        
        $integerOnly = array('cityid', 'isactor', 'isamateuractor', 'hasfilms', 'isemcee', 'istvshowmen',
            'isstatist', 'ismassactor', 'isparodist', 'istwin', 'ismodel', 'isphotomodel', 'ispromomodel', 
            'isdancer', 'hasawards', 'isstripper', 'issinger', 'ismusician', 'issportsman', 'isextremal', 
            'isathlete', 'hasskills', 'hastricks','haslanuages', 'hasinshurancecard', 'countryid', 
            'nativecountryid', 'shoessize', 'rating', 'playagemin', 'playagemax', 'istheatreactor', 
            'ismediaactor', 'currentcountryid', 'chestsize', 'waistsize', 'hipsize', 'height', 'weight', 
        );
        foreach ( $integerOnly as $fieldName )
        {
            $rules[$fieldName][] = array($fieldName, 'numerical', 'integerOnly' => true);
        }
        
        $trim = array('birthdate', 'formattedBirthDate', 'firstname', 'lastname', 'middlename',
            'gender', 'galleryid', 'mobilephone', 'homephone', 'addphone', 'vkprofile', 'fbprofile', 'okprofile',
            'passportdate', 'height', 'weight', 'wearsize', 'looktype', 'haircolor', 'hairlength', 'eyecolor', 
            'cityid', 'passportexpires','chestsize', 'waistsize', 'hipsize',
        );
        foreach ( $trim as $fieldName )
        {
            $rules[$fieldName][] = array($fieldName, 'filter', 'filter' => 'trim');
        }
        
        if ( $field->isRequiredFor('vacancy', $this->vacancy->id) /*AND ! Yii::app()->user->checkAccess('Admin')*/ )
        {// некоторые поля анкеты могут быть обязательными при подаче заявки, но админов
            // которые регистрируют заявки вручную это не касается
            $rules[$field->name][] = array($field->name, 'required');
        }
        
        if ( $this->questionary->id )
        {
            // @todo
        }else
        {
            $rules['email'][] = array('email', 'unique', 'className' => 'User');
        }
        
        return $rules[$field->name];
    }
    
    /**
     * Получить набор правил для дополнительного поля
     * @param ExtraField $field - поле, для которого нужно получить правила формы
     * @return array
     */
    protected function getExtraFieldRules(ExtraField $field)
    {
        $fieldName = $this->extraFieldPrefix.$field->name;
        $rules = array(
            array($fieldName, 'filter', 'filter' => 'trim'),
            array($fieldName, 'length', 'max' => 4000),
        );
        
        if ( $field->isRequiredFor('vacancy', $this->vacancy->id) )
        {
            $rules[] = array($fieldName, 'required');
        }
        return $rules;
    }
    
    /**
     * Перенести данные из модели анкеты и связаных с анкетой таблиц в модель динамической формы (этот класс)
     * @return void
     */
    protected function setUpData()
    {
        if ( ! $this->questionary OR ! $this->vacancy )
        {// можем начинать только если есть все необходимые параметры
            return;
        }
        
        foreach ( $this->extraFields as $name => $extraField )
        {// загружаем в форму все значения доп. полей
            $fieldName = $this->extraFieldPrefix.$extraField->name;
            $this->_attributes[$fieldName] = $extraField->getValueFor('vacancy', $this->vacancy->id, $this->questionary->id);
        }
        foreach ( $this->userFields as $name => $userField )
        {// загружаем в форму все значения из анкеты
            if ( isset($this->questionary->$name) )
            {
                $this->$name = $this->questionary->$name;
            }
        }
        
        if ( $this->gallery = $this->questionary->getGallery() )
        {// галерея с изображениями устанавливается отдельно
            $this->galleryid = $this->gallery->id;
        }
        //CVarDumper::dump($this->rules(), 10, true);die;
        //CVarDumper::dump($this->_attributes, 10, true);die;
    }
    
    /**
     * Фильтр для даты
     * @param string $date
     * @return int
     */
    public function checkInputDate($date)
    {
        return CDateTimeParser::parse($date, Yii::app()->params['inputDateFormat']);
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
        $criteria = new CDbCriteria();
        $criteria->compare('galleryid', $gallery->id);
        if ( $this->questionary->id )
        {
            $criteria->addNotInCondition('id', array($this->questionary->id));
        }
        if ( Questionary::model()->exists($criteria) )
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
        /* @var $questionary Questionary */
        CVarDumper::dump($this, 10, true);die;
        
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
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        $labels = $this->questionary->attributeLabels();
        $extraLabels = array();
        
        foreach ( $this->extraFields as $name => $extraField )
        {
            $extraFieldName = $this->extraFieldPrefix.$name;
            $extraLabels[$extraFieldName] = $extraField->label;
        }
        
        return CMap::mergeArray($labels, $extraLabels);
    }
    
    /**
     * @see CModel::getAttributeLabel()
     */
    public function getAttributeLabel($name)
    {
        $labels = $this->attributeLabels();
        if ( isset($labels[$name]) )
        {
            return $labels[$name];
        }else
        {
            return parent::getAttributeLabel($name);
        }
    }
    
    /**
     * Returns the list of attribute names.
     * By default, this method returns all public properties of the class.
     * You may override this method to change the default.
     * @return array list of attribute names. Defaults to all public properties of the class.
     */
    public function attributeNames()
    {
        return CMap::mergeArray(parent::attributeNames(), array_keys($this->_attributes));
    }
    
    /**
     * Returns the named attribute value.
     * If this is a new record and the attribute is not set before,
     * the default column value will be returned.
     * If this record is the result of a query and the attribute is not loaded,
     * null will be returned.
     * You may also use $this->AttributeName to obtain the attribute value.
     * @param string $name the attribute name
     * @return mixed the attribute value. Null if the attribute is not set or does not exist.
     * @see hasAttribute
     */
    public function getAttribute($name)
    {
        //$extraFieldName = $this->extraFieldPrefix.$name;
        if ( isset($this->_attributes[$name]) )
        {
            return $this->_attributes[$name];
        }elseif ( property_exists($this, $name) )
        {
            return $this->$name;
        }
    }
    
    /**
     * Sets the named attribute value.
     * You may also use $this->AttributeName to set the attribute value.
     * @param string $name the attribute name
     * @param mixed $value the attribute value.
     * @return boolean whether the attribute exists and the assignment is conducted successfully
     * @see hasAttribute
     */
    public function setAttribute($name, $value)
    {
        //$extraFieldName = $this->extraFieldPrefix.$name;
        if ( property_exists($this, $name) )
        {
            $this->$name = $value;
            return true;
        }elseif ( isset($this->_attributes[$name]) )
        {
            $this->_attributes[$name] = $value;
            return true;
        }else
        {
            return false;
        }
        return true;
    }
    
    
    /**
	 * Returns all column attribute values.
	 * Note, related objects are not returned.
	 * @param mixed $names names of attributes whose value needs to be returned.
	 * If this is true (default), then all attribute values will be returned, including
	 * those that are not loaded from DB (null will be returned for those attributes).
	 * If this is null, all attributes except those that are not loaded from DB will be returned.
	 * @return array attribute values indexed by attribute names.
	 */
	public function getAttributes($names=true)
	{
		$attributes  = $this->_attributes;
		$parentNames = parent::attributeNames();
		
		foreach( $parentNames as $name )
		{
			if ( property_exists($this, $name) )
			{
			    $attributes[$name] = $this->$name;
			}elseif ( $names === true && ! isset($attributes[$name]) )
			{
			    $attributes[$name] = null;
			}
		}
		if ( is_array($names) )
		{
			$attrs = array();
			foreach ( $names as $name )
			{
				if ( property_exists($this, $name) )
				{
				    $attrs[$name] = $this->$name;
				}else
				{
				    $attrs[$name] = isset($attributes[$name]) ? $attributes[$name] : null;
				}
			}
			return $attrs;
		}else
		{
		    return $attributes;
		}
	}
		
    
    /**
     * Checks whether this AR has the named attribute
     * @param string $name attribute name
     * @return boolean whether this AR has the named attribute (table column).
     */
    public function hasAttribute($name)
    {
        if ( ! isset($this->_attributes[$name]) )
        {
            return property_exists($this, $name);
        }
        return false;
    }
}