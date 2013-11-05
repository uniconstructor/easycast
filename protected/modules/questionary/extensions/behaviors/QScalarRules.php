<?php

/**
 * Правила проверки для простых полей формы анкеты
 * @todo языковые строки
 */
class QScalarRules extends CActiveRecordBehavior
{
    /**
     * Получить массив стандартных правил проверки (тип данных для входящих значений, длина, trim, и т. д.)
     * @return array
     */
    protected function getDefaultScalarRules()
    {
        return array(
            array('userid, cityid, isactor, isamateuractor, hasfilms, isemcee, istvshowmen,
                        isstatist, ismassactor
                        isparodist, istwin, ismodel, isphotomodel, ispromomodel, isdancer,
                        hasawards, isstripper, issinger,
                        ismusician, issportsman, isextremal, isathlete, hasskills, hastricks,
                        haslanuages, hasinshurancecard, countryid, nativecountryid,
                        shoessize, rating, hastatoo, playagemin, playagemax,
                        istheatreactor, ismediaactor, ownerid',
                'numerical', 'integerOnly' => true),
            
            array('firstname, lastname, middlename, city, inshurancecardnum', 'length', 'max' => 128),
        
            array('mobilephone, homephone, addphone, inn', 'length', 'max' => 32),
            array('hairlength, wearsize', 'length', 'max' => 16),
            array('vkprofile, fbprofile, okprofile, passportorg', 'length', 'max' => 255),
            array('privatecomment, admincomment, moderationcomment', 'length', 'max' => 4095),
            array('passportserial, passportnum', 'length', 'max' => 10),
            
            // @todo добавить индивидуальную проверку для каждого типа поля
            array('passportdate, gender, height, weight, wearsize, looktype, haircolor, eyecolor,
                    physiquetype, titsize, chestsize, waistsize, hipsize, striptype
                    striplevel, singlevel, wearsize, status', 'safe'),
            // @todo добавить проверку сложных значений
            array('voicetimbre, addchar, parodist, twin, vocaltype, sporttype, extremaltype, trick, skill', 'safe'),
        );
    }
    
    /**
     * Получить набор правил для проверки полей
     * @return array
     */
    protected function getCustomScalarRules()
    {
        return array(
            // список обязательных полей анкеты участника
            array('firstname, lastname, gender, birthdate', 'required', 'on' => 'update'), // 
            // проверка даты рождения
            //array('birthdate', 'type', 'type' => 'array', 'allowEmpty' => true),
            //array('birthdate', 'filter', 'filter' => array('QScalarRules', 'checkBirthDate')),
            array('birthdate', 'filter', 'filter' => array($this, 'checkBirthDate')),
        );
    }
    
    /**
     * Проверить дату рождения
     * @param string $attribute - введенное значение
     * @param array $params - дополнительные параметры для проверки
     * @return null
     * 
     * @todo всегда сначала пытаться получить unixtime из массива формы и возвращать null только если его
     *       получить не удалось
     */
    public function checkBirthDate($attribute, $params=array())
    {
        if ( $this->owner->scenario != 'update' )
        {
            return;
        }
        
        if ( ! $this->hasBirthDateArray() )
        {
            if ( intval($this->owner->attributes['birthdate']) > 0 )
            {
                return $this->owner->attributes['birthdate'];
            }
            $oldStatus = Questionary::model()->findByPk($this->owner->id)->status;
            $newStatus = $this->owner->attributes['status'];
            $oldBirthDate = -1;
            if ( isset($this->owner->birthdate) )
            {
                $oldBirthDate = $this->owner->attributes['birthdate'];
            }
            if ( in_array($oldStatus, array('unconfirmed', 'draft', 'delayed')) AND
                 in_array($newStatus, array('unconfirmed', 'draft', 'delayed')) )
            {// не проверяем дату рождения если анкета только что создана
                return $oldBirthDate;
            }
        }
        
        $year  = $this->owner->attributes['birthdate']['Year'];
        $month = $this->owner->attributes['birthdate']['Month'];
        $day   = $this->owner->attributes['birthdate']['Day'];
        
        $inputDate = mktime(12, 0, 0, (int)$month, (int)$day, (int)$year);
        $todayDate = mktime(12, 0, 0, date('m'), date('d'), date('Y'));
        
        //CVarDumper::dump($inputDate, 10);echo '|';
        //CVarDumper::dump($todayDate, 10);die;
        
        if ( $inputDate >= $todayDate )
        {// Дата рождения не может быть сегодняшней или больше текущей
            $this->owner->addError('birthdate', "Не указана дата рождения");
        }
        
        return $inputDate;
    }
    
    /**
     * Проверяет, передана ли дата рождения при сохранении модели
     * Дата рождения передается в виде массива только в том случае если модель была сохранена 
     * из формы
     * 
     * @return void
     */
    protected function hasBirthDateArray()
    {
        if ( isset($this->owner->attributes['birthdate']['Year'])  AND
             isset($this->owner->attributes['birthdate']['Month']) AND
             isset($this->owner->attributes['birthdate']['Day']) )
        {
            return true;
        }
        return false;
    }
}