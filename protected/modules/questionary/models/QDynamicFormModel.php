<?php

/**
 * Модель для динамической формы анкеты
 * Определяет нужные проверки (rules) в зависимости от того какие поля нужно вывести
 * Список возможных сценариев:
 * - registration - регистрация + заявка
 * - application  - заявка от существующего участника
 * - finalization - дополнение участником уже введенных данных
 * 
 * @todo определить сценарии формы
 * @todo заменить все проверки $questionary->isNewRecord на $this->scenario
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
    public $looktype;
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
     * @var string
     * @deprecated использовать galleryid вместо этого поля
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
     * @var int
     */
    public $visible;
    // поля модели QDynamicFormModel (этот класс)
    /**
     * @var string префикс для имен всех дополнительных полей формы
     */
    public $extraFieldPrefix = 'ext_';
    /**
     * @var bool - выводить ли фрагменты формы для тех полей анкеты, которые уже заполнены
     */
    public $displayFilled    = true;
    /**
     * @var string - статус в котором анкеты начинают свою жизнь после регистрации
     */
    public $initialStatus    = Questionary::STATUS_UNCONFIRMED;
    
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
    protected $emptyUserFields  = array();
    /**
     * @var ExtraField[] - список не заполненных дополнительных полей, 
     *                     которые нужно указать прежде чем подать заявку на роль
     *                     (не хранятся в анкете, потому что как правило требуются один раз для одной роли)
     */
    protected $emptyExtraFields = array();
    /**
     * @var QUserField[] - список всех полей анкеты, которые нужно указать прежде чем подать заявку на роль
     */
    protected $userFields  = array();
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
    protected $_rules      = array();
    /**
     * @var array - массив с доп. полями анкеты. Нужен чтобы форма воспринимала из как свои родные поля
     */
    protected $_attributes = array();
    
    /**
     * Геттер и сеттер переопределены для того чтобы модель формы считала все 
     * дополнительные поля заявки "своими" и применяла для них стандартную валидацию 
     * 
     * @see CComponent::__get()
     */
    public function __get($name)
    {
        if ( array_key_exists($name, $this->_attributes) )
        {
            return $this->_attributes[$name];
        }else
        {
            return parent::__get($name);
        }
    }
    
    /**
     * Геттер и сеттер переопределены для того чтобы модель формы считала все 
     * дополнительные поля заявки "своими" и применяла для них стандартную валидацию
     * 
     * @see CComponent::__set()
     */
    public function __set($name, $value)
    {
        if ( array_key_exists($name, $this->_attributes) )
        {
            $this->_attributes[$name] = $value;
        }else
        {
            parent::__set($name, $value);
        }
    }
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        parent::init();
        
        Yii::import('ext.LPNValidator.LPNValidator');
        // обработчики событий
        $qModule    = Yii::app()->getModule('questionary');
        $userModule = Yii::app()->getModule('user');
        // запомним благодаря какой роли произошла регистрация
        $this->attachEventHandler('onNewUserCreatedByVacancy', array($qModule, 'updateCreationHistory'));
        // отправим письмо с паролем если нужно
        $this->attachEventHandler('onNewRegistration', array($userModule, 'notify'));
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
     * Определить, нужно ли участнику указывать дополнительную информацию для подачи заявки
     * или он уже заполнил все необходимые поля до этого (обязательные и дополнительные)
     * Эта функция должна вызываться только после setUpFieldList()
     * @return bool
     */
    public function hasFullInfo()
    {
        if ( empty($this->userFields) AND empty($this->extraFields) )
        {// ничего заполнять не нужно - значит все данные уже есть
            return true;
        }
        return false;
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
        $userFields  = QUserField::model()->forVacancy($this->vacancy)->findAll();
        $extraFields = ExtraField::model()->forVacancy($this->vacancy)->findAll();
        
        foreach ( $userFields as $userField )
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
        foreach ( $extraFields as $extraField )
        {/* @var $extraField ExtraField */
            if ( ! $this->questionary->id OR $extraField->isEmptyForVacancy($this->vacancy, $this->questionary) )
            {// определяем каких полей для этой роли не хватает в заявке и храним их отдельно
                // это нужно для того чтобы была возможность отобразить форму только с необходимым минимумом полей 
                // (для для тех кто уже подал заявку, но получил сообщение о необходимости дополнить информацию)
                $this->emptyExtraFields[$extraField->name] = $extraField;
            }
            // параллельно составляем полный список дополнительных полей
            $this->extraFields[$extraField->name] = $extraField;
            // и также записываем эти же поля в модель формы, чтобы форма думала что это ее родные поля
            $this->_attributes[$this->extraFieldPrefix.$extraField->name] = null;
        }
        if ( ! $this->displayFilled )
        {// убираем из списка полей те которые уже заполнены (если их нужно скрыть)
            // @todo индивидуальная настройка для каждого поля
            $this->userFields  = array_intersect_key($this->userFields, $this->emptyUserFields);
            $this->extraFields = array_intersect_key($this->extraFields, $this->emptyExtraFields);
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
        {// добавляем правила для дополнительных полей
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
                /*array('mobilephone', 'LPNValidator',
                    'defaultCountry' => 'RU',
                    'message'        => 'Неправильно указан номер телефона',
                    'emptyMessage'   => 'Не указан номер телефона',
                ),*/
            ),
            'homephone' => array(
                array('homephone', 'length', 'max' => 32),
                /*array('homephone', 'LPNValidator',
                    'defaultCountry' => 'RU',
                    'message'        => 'Неправильно указан номер телефона',
                    'emptyMessage'   => 'Не указан номер телефона',
                ),*/
            ),
            'addphone' => array(
                array('addphone', 'length', 'max' => 32),
                /*array('addphone', 'LPNValidator',
                    'defaultCountry' => 'RU',
                    'message'        => 'Неправильно указан номер телефона',
                    'emptyMessage'   => 'Не указан номер телефона',
                ),*/
            ),
            'email' => array(
                array('email', 'email'),
            ),
            'policyagreed' => array(
                array(
                    'policyagreed', 'compare',
                    'compareValue' => 1,
                    'message'      => 'Для регистрации требуется ваше согласие',
                ),
            ),
            // проверка даты через фильтр
            'birthdate' => array(
                array(
                    'birthdate', 'date',
                    'allowEmpty' => false,
                    'format'     => Yii::app()->params['yiiDateFormat'],
                ),
            ),
            array('passportexpires', 'ext.YiiConditionalValidator',
                'if' => array(
                    array(
                        'passportexpires', 'date',
                        'allowEmpty' => false,
                        'format'     => Yii::app()->params['yiiDateFormat'],
                    ),
                ),
                'then' => array(
                    array(
                        'passportexpires', 'filter',
                        'filter' => array('EcDateTimeParser', 'parse'),
                    ),
                ),
            ),
            'photo' => array(
                array('photo', 'safe'),
            ),
            // @todo устанавливать эти правила только если одно из этих полей
            //       установлено как обязательное
            /*'chestsize' => array(
                array('chestsize', 'compare', 'compareValue' => 1, 'operator' => '>', 
                    'message' => 'Не указаны параметры тела (объем груди)',
                ),
            ),
            'waistsize' => array(
                array('waistsize', 'compare', 'compareValue' => 1, 'operator' => '>',
                    'message' => 'Не указаны параметры тела (объем талии)',
                ),
            ),
            'hipsize' => array(
                array('hipsize', 'compare', 'compareValue' => 1, 'operator' => '>',
                    'message' => 'Не указаны параметры тела (объем бедер)',
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
            ),*/ 
        );
        
        $integerOnly = array('cityid', 'isactor', 'isamateuractor', 'hasfilms', 'isemcee', 'istvshowmen',
            'isstatist', 'ismassactor', 'isparodist', 'istwin', 'ismodel', 'isphotomodel', 'ispromomodel', 
            'isdancer', 'hasawards', 'isstripper', 'issinger', 'ismusician', 'issportsman', 'isextremal', 
            'isathlete', 'hasskills', 'hastricks','haslanuages', 'hasinshurancecard', 'countryid', 
            'nativecountryid', 'shoessize', 'rating', 'playagemin', 'playagemax', 'istheatreactor', 
            'ismediaactor', 'currentcountryid', 'chestsize', 'waistsize', 'hipsize', 'height', 'weight', 
            'galleryid', 'visible',
        );
        foreach ( $integerOnly as $fieldName )
        {
            $rules[$fieldName][] = array($fieldName, 'numerical', 'integerOnly' => true);
        }
        $trim = array('birthdate', 'formattedBirthDate', 'firstname', 'lastname', 'middlename',
            'gender', 'galleryid', 'mobilephone', 'homephone', 'addphone', 'vkprofile', 
            'fbprofile', 'okprofile', 'passportdate', 'height', 'weight', 'wearsize', 
            'looktype', 'haircolor', 'hairlength', 'eyecolor', 'city', 'cityid', 
            'passportexpires', 'chestsize', 'waistsize', 'hipsize',
        );
        foreach ( $trim as $fieldName )
        {
            $rules[$fieldName][] = array($fieldName, 'filter', 'filter' => 'trim');
        }
        
    	if ( $field->isRequiredForVacancy($this->vacancy) 
             AND ! Yii::app()->user->checkAccess('Admin') 
             AND ! $field->multiple )
        {// некоторые поля анкеты могут быть обязательными при подаче заявки, но админов
            // которые регистрируют заявки вручную это не касается
            // списки полей также не могут быть обязательными: они сохраняются отдельно от формы
            $rules[$field->name][] = array($field->name, 'required');
        }
        if ( $field->isForcedForVacancy($this->vacancy) AND $field->isEmptyIn($this->questionary) )
        {// некоторые поля анкеты могут быть установлены автоматически, если не заполнены
            $default = QFieldInstance::model()->attachedTo('vacancy', $this->vacancy->id)->
                forField($field->id)->find();
            $rules[$field->name][] = array($field->name, 'default', 
                'setOnEmpty' => false, 
                'value'      => $default->data,
            );
        }
        
        if ( $this->questionary->id )
        {
            // @todo проверить уникальность email в случае редактирования анкеты
        }else
        {
            $rules['email'][] = array('email', 'unique', 'className' => 'User');
        }
        if ( ! isset($rules[$field->name]) )
        {// нет проверок для этого типа поля: возвращяем заглушку и пишем ошибку в лог
            Yii::log('QDynamicFormModel: no rules for unknown field '.$field->name, CLogger::LEVEL_ERROR);
            return array(array($field->name, 'safe'));
        }
        return $rules[$field->name];
    }
    
    /**
     * Получить набор правил для дополнительного поля
     * 
     * @param ExtraField $field - поле, для которого нужно получить правила формы
     * @return array
     */
    protected function getExtraFieldRules(ExtraField $field)
    {
        $fieldName = $this->extraFieldPrefix.$field->name;
        $rules = array(
            array($fieldName, 'filter', 'filter' => 'trim'),
            array($fieldName, 'length', 'max' => 4095),
        );
        if ( $field->isRequiredForVacancy($this->vacancy) AND ! Yii::app()->user->checkAccess('Admin') )
        {
            $rules[] = array($fieldName, 'required');
        }
        return $rules;
    }
    
    /**
     * Перенести данные из модели анкеты и связаных с анкетой таблиц 
     * в модель динамической формы (этот класс)
     * 
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
            if ( ! $this->questionary->id )
            {
                $this->_attributes[$fieldName] = '';
            }else
            {
                $this->_attributes[$fieldName] = (string)$extraField->
                    getValueForVacancy($this->vacancy, $this->questionary->id);
            }
        }
        foreach ( $this->userFields as $name => $userField )
        {// загружаем в форму все значения из анкеты
            if ( isset($this->questionary->$name) )
            {
                $this->$name = $this->questionary->$name;
            }
        }
        if ( ! $this->scenario != 'registration' AND $gallery = $this->questionary->getGallery() )
        {// галерея с изображениями устанавливается отдельно
            $this->galleryid = $gallery->id;
        }
    }
    
    /**
     * Фильтр для даты
     * 
     * @param  string $date
     * @return int
     * 
     * @deprecated
     */
    /*public function checkInputDate($date)
    {
        if ( strpos($date, '.') === false )
        {// дата из базу уже в unixtime: она не нуждается в преобразовании
            return $date;
        }
        return CDateTimeParser::parse($date, Yii::app()->params['inputDateFormat']);
    }*/
    
    /**
     * Эта функция проверяет обязательное наличие хотя бы одной загруженной фотографии
     * 
     * @see CModel::beforeValidate()
     */
    public function beforeValidate()
    {
        if ( ! $this->hasPhotos($this->galleryid) )
        {
            $this->addError('galleryid', '<div class="alert alert-block alert-error">
                Нужно загрузить хотя бы одну фотографию</div>');
        }
        return parent::beforeValidate();
    }
    
    /**
     * Эта функция проверяет обязательное наличие хотя бы одной загруженной фотографии
     * 
     * @return bool
     */
    /*protected function validateGalleryId()
    {
        if ( $this->hasPhotos($this->galleryid) )
        {// фотографии есть, все ОК
            return true;
        }
        // фотографии не загружены - добавляем в общий список сообщение об ошибке
        $this->addError('galleryid', '<div class="alert alert-block alert-error">
            Нужно загрузить хотя бы одну фотографию</div>');
        // возвращаем false чтобы прервать дальшейшие проверки
        return false;
    }*/
    
    /**
     * @see CModel::validate()
     */
    public function validate($attributes=null, $clearErrors=true)
    {
        return parent::validate($attributes, $clearErrors);
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
        {// галерея не найдена - значит нет и фотографий
            return false;
        }
        // создаем условие для проверки того существует ли уже анкета с такой галереей
        $criteria = new CDbCriteria();
        $criteria->compare('galleryid', $gallery->id);
        
        if ( isset($this->questionary->id) AND $this->questionary->id )
        {// если заявка подается от существующего участника
            //$criteria->addNotInCondition('id', array($this->questionary->id));
            $criteria->compare('id', '<>'.$this->questionary->id);
        }
        if ( $this->scenario === 'registration' AND Questionary::model()->exists($criteria) )
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
     * Создать анкету, одновременно подав заявку на роль
     * Заявки, поданые через регистрацию не проверяются на соответствие критериям поиска
     * (такой небольшой бонус в честь регистрации)
     * @return User
     *
     * @todo обработать возможные ошибки
     */
    public function save()
    {
        /* @var $questionary Questionary */
        /* @var $user User */
        if ( $this->scenario === 'registration' )
        {// создаем пользователя, если это регистрация
            $user           = new User();
            $sourcePassword = $user->generatePassword();
            // заполняем недостающие поля
            $user->email        = $this->email;
            $user->username     = $user->getLoginByEmail($user->email);
            $user->password     = UserModule::encrypting($sourcePassword);
            $user->activkey     = UserModule::encrypting(microtime().$user->password);
            $user->superuser    = 0;
            $user->status       = User::STATUS_ACTIVE;
            $user->policyagreed = 1;
            // сохраняем пользователя, вместе с ним автоматически создается анкета 
            // и все связанные с анкетой записи
            if ( ! $user->save() )
            {
                throw new CException('Не удалось создать пользователя'.implode(', ', $user->getErrors()));
            }
        }else
        {// берем пользователя из анкеты, если запись на роль производится администратором
            // или заявку подает уже зарегистрированый участник
            $user = $this->questionary->user;
        }
        // @todo дописать сеттеры
        unset($this->userFields['email']);
        unset($this->userFields['policyagreed']);
        // @todo переименовать в galleryid
        unset($this->userFields['photo']);
        
        // заполняем и сохраняем анкету
        $this->saveQuestionary($user->questionary);
        // сохраняем доп. поля для этой роли
        $this->saveExtraFields($user->questionary);
        
        if ( $this->scenario != 'finalization' )
        {// создаем новую заявку на роль (если это не дополнение существующих данных)
            $this->saveMemberRequest($user->questionary);
        }
        if ( $this->scenario === 'registration' )
        {// если это регистрация - отправляем пользователю письмо с приглашением и паролем
            $this->sendUserNotification($user->questionary, $sourcePassword);
            // обновляем историю создания анкет
            $this->updateCreationHistory($user->questionary);
        }
        return $user;
    }
    
    /**
     * Сохранить данные анкеты при регистрации или подаче заявки на роль
     * @param Questionary $questionary
     * @return void
     */
    protected function saveQuestionary($questionary)
    {
        $oldGallery = $questionary->getGallery();
        foreach ( $this->userFields as $name => $field )
        {// сохраняем все поля анкеты
            $questionary->$name = $this->$name;
        }
        if ( $this->scenario === 'registration' )
        {// новым созданным анкетам проставляется статус "требует проверки"
            $questionary->status = $this->initialStatus;
        }
        // Устанавливаем и сохраняем условия съемок
        $questionary->recordingconditions->save();
        
        if ( $this->scenario === 'registration' AND $oldGallery AND $oldGallery->id != $this->galleryid )
        {// заменяем по умолчанию созданную галерею на новую (только при регистрации)
            $questionary->galleryid = $this->galleryid;
            $oldGallery->delete();
        }
        // сохраняем анкету (без проверки полей, она уже произведена здесь)
        if ( ! $questionary->save() )
        {
            throw new CException('Не удалось создать анкету');
        }
        // сохраняем галерею еще раз чтобы автоматически проставилась главная фотка
        $questionary->getGallery()->save();
    }
    
    /**
     * Сохранить дополнительные поля анкеты
     * @param Questionary $questionary
     * @return void
     */
    protected function saveExtraFields($questionary)
    {
        foreach ( $this->extraFields as $extraField )
        {
            $name      = $this->extraFieldPrefix.$extraField->name;
            $newValue  = $this->$name;
            
            if ( $value = ExtraFieldValue::model()->
                    forVacancy($this->vacancy)->
                    forQuestionary($questionary)->
                    forField($extraField->id)->find() )
            {// значение для этого поля уже сохранено - просто обновим его
                $value->value = $newValue;
                $value->save();
            }else
            {// объект значения еще не создан в базе для этой анкеты
                $instance = ExtraFieldInstance::model()->
                    forVacancy($this->vacancy)->
                    forField($extraField->id)->find();
                if ( ! $instance )
                {// @todo записать в лог
                    throw new CException('Instance not found');
                    return false;
                }
                // создаем запись значения дополнительного поля и привязываем ее к анкете
                $value                = new ExtraFieldValue;
                $value->instanceid    = $instance->id;
                $value->questionaryid = $questionary->id;
                $value->value         = $newValue;
                $value->save();
            }
        }
    }
    
    /**
     * Подать заявку на роль (вне зависимости от условий поиска если это регистрация)
     * @param Questionary $questionary
     * @return void
     */
    public function saveMemberRequest($questionary)
    {
        $request = new MemberRequest();
        $request->vacancyid = $this->vacancy->id;
        $request->memberid  = $questionary->id;
        $request->save();
    }
    
    /**
     * Обновить историю создания анкет, если заявка на роль была подана через регистрацию 
     * @param Questionary $questionary
     * @return void
     */
    protected function updateCreationHistory($questionary)
    {
        if ( $this->scenario === 'registration' OR $this->questionary->isNewRecord )
        {// если пользователь зарегистрировался через подачу заявки на эту роль - запомним это
            // отправив событие, дополняющее историю создания анкет (QCreationHistory)
            $event = new CModelEvent($this, array(
                'questionaryId' => $questionary->id,
                'objectType'    => 'vacancy',
                'objectId'      => $this->vacancy->id,
            ));
            $this->onNewUserCreatedByVacancy($event);
        }
    }
    
    /**
     * Отправить пользователю письмо с приглашением и паролем
     * 
     * @param Questionary $questionary
     * @param string $sourcePassword
     * @return void
     * 
     * @todo брать адрес почты в зависимости от роли, переименовать action
     */
    protected function sendUserNotification($questionary, $sourcePassword)
    {
        if ( ! $this->scenario === 'registration' OR $this->questionary->isNewRecord )
        {// высылаем пользователю подтверждение регистрации с логином  и паролем
            $params = array(
                'channel' => 'email',
                'action'  => 'MCRegistration',
                'params'  => array(
                    'questionary' => $questionary,
                    'vacancy'     => $this->vacancy,
                    'password'    => $sourcePassword,
                ),
                'subject' => 'Ваша заявка на участие в кастинге проекта «'.$this->vacancy->event->project->name.'» зарегистрирована',
                'sendNow' => true,
                'email'   => $questionary->user->email,
                'from'    => 'admin@easycast.ru',
            );
            $event = new CModelEvent($this, $params);
            $this->onNewRegistration($event);
        }
    }
    
    /**
     * 
     * @param CModelEvent $event
     * @return void
     */
    public function onNewUserCreatedByVacancy($event)
    {
        $this->raiseEvent('onNewUserCreatedByVacancy', $event);
    }
    
    /**
     * 
     * @param CModelEvent $event
     * @return void
     */
    public function onNewRegistration($event)
    {
        $this->raiseEvent('onNewRegistration', $event);
    }
    
    // служебные функции для работы с атрибутами модели: их нужно было переписать, 
    // для того чтобы эта модель могла обращаться с произвольным количеством 
    // дополнительных полей так как будто они были заранее объявлеными свойствами
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        $labels      = $this->questionary->attributeLabels();
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
        if ( isset($this->_attributes[$name]) )
        {
            $this->_attributes[$name] = $value;
            return true;
        }elseif ( property_exists($this, $name) )
        {
            $this->$name = $value;
            return true;
        }else
        {
            return false;
        }
    }
    
    /**
	 * Sets the attribute values in a massive way.
	 * @param array $values attribute values (name=>value) to be set.
	 * @param boolean $safeOnly whether the assignments should only be done to the safe attributes.
	 * A safe attribute is one that is associated with a validation rule in the current {@link scenario}.
	 * @see getSafeAttributeNames
	 * @see attributeNames
	 */
	public function setAttributes($values,$safeOnly=true)
	{
		if( ! is_array($values) )
		{
		    return;
		}
		
		$attributes = array_flip($safeOnly ? $this->getSafeAttributeNames() : $this->attributeNames());
		foreach($values as $name=>$value)
		{
			if( isset($attributes[$name]) )
			{
			    if ( ! $this->setAttribute($name, $value) )
			    {
			        $this->$name = $value;
			    }
			}elseif ( $safeOnly )
			{
			    $this->onUnsafeAttribute($name,$value);
			}
		}
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
}