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
     * Получить название города проживания
     */
    public function getCity()
    {
        if ( $this->owner->cityid )
        {
            $CountryCitySelector = new CountryCitySelectorRu();
            return $CountryCitySelector->getCity($this->owner->cityid)->name;
        }
    
        return CHtml::encode($this->owner->city);
    }
    
    /**
     * Получить id города
     * @return int
     */
    public function getCityid()
    {
        if ( ! $this->owner->cityid AND Yii::app()->user->isSuperuser AND ! $this->owner->timemodified )
        {// если анкета только создана и заполняется администратором - 
            // то устанавливаем Москву в качестве города по умолчанию 
            return $this->getDefaultCityId();
        }
        $this->owner->cityid;
    }
    
    /**
     * Получить размер одежды
     * @return string
     * 
     * @deprecated оставлено для совместимости
     * @todo удалить при рефакторинге
     */
    public function getWearsize()
    {
        if ( $this->owner->scenario == 'view' )
        {
            return $this->getScalarFieldDisplayValue('wearsize', $this->owner->wearsize);
        }
    
        return $this->owner->wearsize;
    }
    
    /**
     * Установить размер одежды
     * @param string $value
     * 
     * @deprecated оставлено для совместимости
     * @todo удалить при рефакторинге
     */
    public function setWearsize($value)
    {
        $this->owner->wearsize = $value;
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
    public function getPassportexpires($value)
    {
        if ( $this->owner->scenario == 'view' )
        {
            return date('d.m.Y', $this->owner->recordingconditions->passportexpires);
        }
        
        return $this->owner->recordingconditions->passportexpires;
    }
    
    /**
     * Установить срок истечения загранпаспорта
     * @deprecated после переноса этих данных в раздел "условия участия в съемках" не используется
     *               удалить этот метод при рефакторинге
     */
    public function setPassportexpires($value)
    {
        $this->owner->passportexpires = ActiveDateSelect::make_unixtime($value);
    }
    
    /**
     * Установить дату выдачи обычного паспорта
     */
    public function setPassportdate($value)
    {
        $this->owner->passportdate = ActiveDateSelect::make_unixtime($value);
    }
    
    /**
     * Получить возраст (целым числом)
     *
     * @return int
     * @todo разобраться с тем как флексия слов настраивается через языковые строки
     */
    public function getAge()
    {
        $age = floor((time() - $this->owner->birthdate) / (3600 * 24 * 365) );
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
            return $age.' '.$langString;
            //return QuestionaryModule::t('age', array('{age}' => $age));
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
    
    /*public function getCountryid()
    {
        
    }
    
    public function getNativecountryid()
    {
    
    }*/
    
    public function getWantsbusinesstrips()
    {
        if ( $this->owner->recordingconditions )
        {
            return $this->owner->recordingconditions->wantsbusinesstrips;
        }
    }
    
    public function getHasforeignpassport()
    {
        if ( $this->owner->recordingconditions )
        {
            return $this->owner->recordingconditions->hasforeignpassport;
        }
    }
    
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
        $this->owner->admincomment = CHtml::encode($comment);
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
        
        return CHtml::encode($fullname);
    }
    
    /**
     * Получить игровой возраст 
     * @return string|NULL
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
     * @deprecated
     * Получить тип лица
     */
    public function getFacetype()
    {
        if ( $this->owner->scenario == 'view' AND $this->owner->facetype )
        {
            return $this->getScalarFieldDisplayValue('facetype', $this->owner->facetype);
        }
    
        return $this->owner->facetype;
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
        if ( isset($variants[Questionary::VALUE_NOT_SET]) ) unset($variants[Questionary::VALUE_NOT_SET]);
        
        if ( ! isset($variants[$value]) )
        {
            return $value;//'[['.$value.']](translation not found)';
        }
        
        return $variants[$value];
    }
    
    /**
     * Получить город, стоящий по умолчанию в анкете
     * @return number
     */
    public function getDefaultCityId()
    {
        // @todo Сейчас стоит москва - возможно потом это следует сделать настройкой
        return 4400;
    }
}