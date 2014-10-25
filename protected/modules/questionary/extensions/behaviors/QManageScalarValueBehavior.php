<?php

/**
 * Класс, отвечающий за сохранение и получение обычных полей анкеты
 * 
 * @property Questionary $owner
 */
class QManageScalarValueBehavior extends CActiveRecordBehavior
{
    /**
     * @var string
     */
    protected $_formattedBirthDate;
    
    /**
     * @return string
     */
    public function getEmail()
    {
        if ( $this->owner->user )
        {
            return $this->owner->user->email;
        }
        return null;
    }
    
    /**
     * @return string
     */
    public function getPolicyagreed()
    {
        if ( $this->owner->user )
        {
            return $this->owner->user->policyagreed;
        }
        return null;
    }
    
    /**
     * 
     * @param int|string $city
     * @return void
     */
    public function setCity($city)
    {
        $city = trim($city);
        if ( $cityId = intval($city) )
        {
            $this->owner->cityid = $cityId;
            return;
        }
        $criteria = new CDbCriteria();
        $criteria->compare('name', $city);
        $criteria->compare('countryid', 3159);
        
        if ( $record = CSGeoCity::model()->find($criteria) )
        {
            $this->owner->cityid = $record->id;
        }else
        {
            $this->owner->city   = $city;
        }
    }
    
    /**
     * Получить название города проживания
     */
    public function getCity()
    {
        if ( $this->owner->cityid AND $city = CSGeoCity::model()->findByPk($this->owner->cityid) )
        {
            return $city->name;
        }
        return $this->owner->city;
    }
    
    /**
     * Получить название города проживания
     */
    public function getCityName()
    {
        if ( $this->owner->cityid AND $city = CSGeoCity::model()->findByPk($this->owner->cityid) )
        {
            return $city->name;
        }
        return $this->owner->city->name;
    }
    
    /**
     * 
     * @param string|int $city
     * @return void
     */
    public function setCityId($city)
    {
        $this->setCity($city);
    }
    
    /**
     * Получить id города
     * @return int
     */
    public function getCityId()
    {
        if ( ! $this->owner->cityid AND Yii::app()->user->checkAccess('Admin') AND ! $this->owner->timemodified )
        {// если анкета только создана и заполняется администратором - 
            // то устанавливаем Москву в качестве города по умолчанию 
            return $this->getDefaultCityId();
        }
        return $this->owner->cityid;
    }
    
    /**
     * 
     * @return string
     */
    public function getRegionName()
    {
        if ( ! $city = $this->owner->cityobj )
        {/* @var $city CSGeoCity */
            return null;
        }
        return $city->region->name;
    }
    
    /**
     * Получить название страны (гражданство)
     * @return string
     */
    public function getCountryName()
    {
        if ( $this->owner->country )
        {
            return $this->owner->country->name;
        }
        return '';
    }
    
    /**
     * Получить дату рождения
     */
    public function getFormattedBirthDate()
    {
        if ( $this->_formattedBirthDate )
        {
            return $this->_formattedBirthDate;
        }else
        {
            $format = Yii::app()->params['outputDateFormat'];
            $date = date($format, (int)$this->owner->birthdate);
            if ( $date != date($format, 0) )
            {
                return $date;
            }
        }
    }
    
    /**
     * Получить дату рождения
     */
    public function setFormattedBirthDate($date)
    {
        $this->_formattedBirthDate = $date;
        $this->owner->birthdate = CDateTimeParser::parse($date, Yii::app()->params['inputDateFormat']);
    }
    
    /**
     * Получить срок истечения загранпаспорта
     */
    public function getPassportExpires()
    {
        if ( ! $this->owner->recordingconditions )
        {
            return;
        }
        return $this->owner->recordingconditions->passportexpires;
    }
    
    /**
     * 
     * @param int $date
     * @return void
     */
    public function setPassportExpires($date)
    {
        if ( ! $this->owner->recordingconditions )
        {
            return;
        }
        $this->owner->recordingconditions->passportexpires = $date;
    }
    
    /**
     * Получить возраст (целым числом)
     *
     * @return int
     * @todo разобраться с тем как флексия слов настраивается через языковые строки
     *       использовать QuestionaryModule::t('age', array('{age}' => $age)); ну или что-то типа того
     */
    public function getAge()
    {
        $age = floor((time() - $this->owner->birthdate) / (3600 * 24 * 365));
        if ( $this->owner->birthdate AND $age > 0 )
        {
            switch ( mb_substr($age, -1) )
            {
                case '1': $langString = 'год'; break;
                case '2': 
                case '3': 
                case '4': $langString = 'года'; break;
                default : $langString = 'лет'; break;
            }
            if ( $age > 10 AND $age < 15 )
            {// этот непредсказуемый русский язык :) "11 лет" и "21 год"
                $langString = 'лет';
            }
            if ( Yii::app()->language == 'ru' )
            {// в русском языке принято писать "возраст: 23 года", а в английском просто "age: 23"
                return $age.' '.$langString;
            }else
            {
                return $age.' ';
            }
        }
        return null;
    }
    
    /**
     * Получить тип внешности 
     * @return string
     */
    public function getLooktype()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->looktype )
        {
            return $this->getScalarFieldDisplayValue('looktype', $this->owner->looktype);
        }
        return $this->owner->looktype;
    }
    
    /**
     * Получить цвет волос
     * @return string
     */
    public function getHaircolor()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->haircolor )
        {
            return $this->getScalarFieldDisplayValue('haircolor', $this->owner->haircolor);
        }
    
        return $this->owner->haircolor;
    }
    
    /**
     * Получить цвет глаз
     * @return string
     */
    public function getEyecolor()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->eyecolor)
        {
            return $this->getScalarFieldDisplayValue('eyecolor', $this->owner->eyecolor);
        }
    
        return $this->owner->eyecolor;
    }
    
    /**
     * Получить телосложение
     * @return string
     */
    public function getPhysiquetype()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->physiquetype)
        {
            return $this->getScalarFieldDisplayValue('physiquetype', $this->owner->physiquetype);
        }
    
        return $this->owner->physiquetype;
    }
    
    /**
     * Получить размер груди
     * @return string
     */
    public function getTitsize()
    {
        if ( $this->owner->scenario == 'view' )
        {
            return $this->getScalarFieldDisplayValue('titsize', $this->owner->titsize);
        }
    
        return $this->owner->titsize;
    }
    
    /**
     * Получить обхват груди
     * @return string
     */
    public function getChestsize()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->chestsize )
        {
            return $this->owner->chestsize.' '.QuestionaryModule::t('cm');
        }
    
        return $this->owner->chestsize;
    }
    
    /**
     * Получить размер талии
     * @return string
     */
    public function getWaistsize()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->waistsize )
        {
            return $this->owner->waistsize.' '.QuestionaryModule::t('cm');
        }
    
        return $this->owner->waistsize;
    }
    
    /**
     * Получить ширину бедер
     * @return string
     */
    public function getHipsize()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->hipsize )
        {
            return $this->owner->hipsize.' '.QuestionaryModule::t('cm');
        }
    
        return $this->owner->hipsize;
    }
    
    /**
     * Получить рост
     * @return string
     */
    public function getHeight()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->height )
        {
            return $this->owner->height.' '.QuestionaryModule::t('cm');
        }
    
        return $this->owner->height;
    }
    
    /**
     * Получить вес
     * @return string
     */
    public function getWeight()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->weight )
        {
            return $this->owner->weight.' '.QuestionaryModule::t('kg');
        }
        return $this->owner->weight;
    }
    
    /**
     * Заглушка для нормального сохранения галереи ихображений при регистрации
     * @param unknown $photo
     * @return void
     */
    public function setPhoto($photo)
    {
        return;
    }
    
    public function getStriptype()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->striptype )
        {
            return $this->getScalarFieldDisplayValue('striptype', $this->owner->striptype);
        }
        
        return $this->owner->striptype;
    }
    
    public function getStriplevel()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->striplevel )
        {
            return $this->getScalarFieldDisplayValue('striplevel', $this->owner->striplevel);
        }
    
        return $this->owner->striplevel;
    }
    
    public function getSinglevel()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->singlevel )
        {
            return $this->getScalarFieldDisplayValue('level', $this->owner->singlevel);
        }
        
        return $this->owner->singlevel;
    }
    
    public function getWantsbusinesstrips()
    {
        if ( $this->owner->recordingconditions )
        {
            return $this->owner->recordingconditions->wantsbusinesstrips;
        }
    }
    
    /**
     * 
     * @return number
     */
    public function getHasforeignpassport()
    {
        if ( $this->owner->recordingconditions )
        {
            return $this->owner->recordingconditions->hasforeignpassport;
        }
    }
    
    /**
     * Получить размер оплаты
     * @return string|NULL
     */
    public function getSalary()
    {
        if ( $this->owner->recordingconditions )
        {
            return $this->owner->recordingconditions->salary;
        }
        return null;
    }
    
    /**
     * Изменить статус анкеты
     * @todo проверить доступные статусы по списку
     * 
     * @param string $newStatus - новый статус анкеты
     * @return bool
     */
    public function setStatus($newStatus)
    {
        $this->owner->status = $newStatus;
        return $this->owner->save(false);
    }
    
    /**
     * Получить статус для отображения пользователю
     * 
     * @return string
     */
    public function getStatustext($status=null)
    {
        if ( ! $status )
        {
            $status = $this->owner->status;
        }
        return QuestionaryModule::t('status_'.$status);
    }
    
    /**
     * Сохранить комментарий админа к анкете 
     * @param string $comment
     * @return boolean
     */
    public function setAdmincomment($comment)
    {
        $this->owner->admincomment = CHtmlPurifier::purify($comment);
        return $this->owner->save();
    }
    
    /**
     * Получить список статусов, в которые может перейти анкета
     * @todo добавить статус "suspended"
     * @todo переписать, используя константы
     * @return array
     */
    public function getAllowedStatuses()
    {
        switch ( $this->owner->status )
        {
            case 'draft':
                return array('unconfirmed', 'active', 'delayed', 'pending');
            break;
            case 'active':
                return array('active', 'pending');
            break;
            case 'pending':
                return array('pending', 'active', 'rejected');
            break;
            case 'unconfirmed':
                return array('unconfirmed', 'active');
            break;
            case 'delayed':
                return array('delayed', 'unconfirmed', 'active');
            break;
            case 'rejected':
                return array('rejected', 'active', 'pending');
            break;
        }
         
        return array();
    }
    
    /**
     * Получить имя и фамилию участника
     */
    public function getFullname()
    {
        $fullname = $this->owner->firstname.' '.$this->owner->lastname;
        
        if ( ! trim($fullname) AND is_object($this->owner->user) )
        {// Если пользователь еще не заолнил анкету - выводим только его логин
            $fullname = $this->owner->user->username;
        }
        if ( Yii::app()->language != 'ru' )
        {// если язык выбран любой язык, кроме русского - выводим ФИО транслитом
            $fullname = ECPurifier::translit($fullname);
        }
        
        return CHtml::encode($fullname);
    }
    
    /**
     * Получить игровой возраст 
     * 
     * @return string|NULL
     * @deprecated больше не используется
     */
    public function getPlayage()
    {
        if ( $this->owner->playagemin AND $this->owner->playagemax )
        {
            if ( $this->owner->playagemin == $this->owner->playagemax )
            {
                return $this->owner->playagemax;
            }
            return $this->owner->playagemin.'-'.$this->owner->playagemax;
        }
        if ( $this->owner->playagemin )
        {
            return $this->owner->playagemin;
        }
        if ( $this->owner->playagemax )
        {
            return $this->owner->playagemax;
        }
        return null;
    }
    
    /**
     * Получить значение скалярного поля для отображения пользователю
     * 
     * @param string $field - название поля
     * @param string $value - значение поля в базе
     * @return string
     */
    public function getScalarFieldDisplayValue($field, $value)
    {
        $variants = $this->owner->getFieldVariants($field);
        
        if ( isset($variants[Questionary::VALUE_NOT_SET]) )
        {
            unset($variants[Questionary::VALUE_NOT_SET]);
        }
        if ( ! isset($variants[$value]) )
        {
            if ( Yii::app()->language === 'ru' )
            {
                return $value;
            }else
            {
                switch ( $field )
                {
                    case 'shoessize': 
                    case 'wearsize': 
                    case 'hipsize': 
                    case 'waistsize': 
                    case 'chestsize': 
                    case 'weight': 
                    case 'height': 
                    case 'hairlength': 
                    case 'titsize': 
                        return $value;
                    break;
                    case 'salary':
                    case 'age':
                    case 'countryName':
                    case 'regionName':
                    case 'cityName':
                        return $this->$value;
                }
                if ( $value )
                {// если значение указано - выведем его перевод
                    return QuestionaryModule::t($field.'_'.$value);
                }else
                {// если значение не указано - ничего не выводим, чтобы в переводе не присутствовало строк вроде 
                    // "looktype_"
                    return '';
                }
            }
        }
        return $variants[$value];
    }
    
    /**
     * Получить город, стоящий по умолчанию в анкете
     * @return number
     */
    public function getDefaultCityId()
    {
        // @todo Сейчас стоит Москва - возможно потом это следует сделать настройкой
        return 4400;
    }
    
    /**
     * Получить данные анкеты для вывода и сравнения их с данными фильтра поиска
     * 
     * @param string $filterName
     * @return string
     */
    public function getFilterFieldData($filterName)
    {
        $result = '';
        switch ( $filterName )
        {
            case 'body':
                $result .= $this->getPropertyData('chestsize').' / ';
                $result .= $this->getPropertyData('waistsize').' / ';
                $result .= $this->getPropertyData('hipsize');
                return $result;
            case 'city': 
            case 'region': $fieldName = $filterName.'id'; break;
            default: $fieldName = $filterName;
        }
        return $this->getPropertyData('chestsize');
    }
    
    /**
     * Получить блок с описанием одного поля анкеты
     * 
     * @param string $field - поле в анкете (как оно называется в базе)
     * @return string
     *
     * @todo убрать дублирование кода с классом QUserInfo
     */
    protected function getPropertyData($field)
    {
        $questionary = $this->owner;
    
        $label       = $questionary->getAttributeLabel($field);
        $value       = '';
        $placeholder = '[нет данных]';
        $affix       = '';
        $hint        = '';
    
        switch ( $field )
        {
            case 'age':
                $placeholder = '[не указан]';
                if ( $value = $questionary->age )
                {
                    $info = explode(' ', $value);
                    $value = $info[0];
                    $affix = $info[1];
                }
                break;
            case 'playage':
                $placeholder = '[не указан]';
                $value = $questionary->playage;
                break;
            case 'looktype':
                if ( $questionary->nativecountry )
                {
                    $hint = $questionary->getAttributeLabel('nativecountryid').': '.$questionary->nativecountry->name;
                }
                $placeholder = '[не указан]';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                break;
            case 'height':
            case 'chestsize':
            case 'waistsize':
            case 'hipsize':
                $affix = 'см';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                break;
            case 'weight':
                $affix = 'кг';
                $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                break;
            case 'addchar':
                if ( ! $value = $this->getAddCharPropertyBlock() )
                {// не выводим поле с дополнительными хакактеристиками если оно не заполнено
                    return;
                }
                break;
            case 'titsize':
                if ( $questionary->gender === 'female' AND $questionary->Titsize )
                {// не выводим поле с дополнительными хакактеристиками если оно не заполнено
                    $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field);
                }
                break;
            case 'cityid':
                if ( $questionary->cityid )
                {
                    $value = $questionary->cityobj->name;
                }elseif ( $questionary->city )
                {
                    $value = $questionary->city;
                }else
                {
                    return;
                }
                break;
            default: $value = $questionary->getScalarFieldDisplayValue($field, $questionary->$field); break;
        }
        if ( ! $value )
        {// значение не указано - выведем заглушку
            return $placeholder;
            // @todo если пользователь - админ то показывать что поле не заполнено
            // $muted = true;
        }
    
        // получаем параметры для виджета с информацией об анкете
        // $options = $this->getPropertyOptions($label, $value, $placeholder, $affix, $hint);
    
        return array($label, $value.' '.$affix);
    }
    
    /**
     * Получить блок с информацией о дополнительных ракактеристиках внешности
     * @return string
     */
    protected function getAddCharPropertyBlock()
    {
        $questionary = $this->owner;
        $misc = '';
        if ( $addchars = $questionary->addchars )
        {
            foreach ( $addchars as $addchar )
            {
                $addchar->setScenario('view');
                if ( trim($addchar->name) )
                {
                    $misc .= $addchar->name.'<br>';
                }
            }
        }
        if ( $questionary->isathlete )
        {// атлет
            $misc .= $questionary->getAttributeLabel('isathlete').'<br>';
        }
        if ( $questionary->hastatoo )
        {// татуировки
            $misc .= QuestionaryModule::t('hastatoo_enabled').'<br>';
        }
    
        return $misc;
    }
}