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
            array('vkprofile, fbprofile, okprofile, passportorg, admincomment', 'length', 'max' => 255),
            array('privatecomment', 'length', 'max' => 4095),
            array('passportserial, passportnum', 'length', 'max' => 10),
            
            // @todo добавить индивидуальную проверку для каждого типа поля
            array('birthdate, passportdate, gender, height, weight, wearsize, looktype, haircolor, eyecolor,
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
            array('firstname, lastname, gender, birthdate', 'required', 'on' => 'update'),
            // проверка даты рождения
            //array('birthdate', 'type', 'type' => 'array'),
            //array('birthdate', 'filter', 'filter' => array('QScalarRules', 'checkBirthDate')),
            array('birthdate', 'filter', 'filter' => array($this, 'checkBirthDate')),
        );
    }
    
    /**
     * Проверить дату рождения
     * @param string $attribute - введенное значение
     * @param array $params - дополнительные параметры для проверки
     * @return null
     */
    public function checkBirthDate($attribute, $params=array())
    {
        if ( $this->owner->scenario != 'update' )
        {
            return;
        }
        
        $year  = $this->owner->attributes['birthdate']['Year'];
        $month = $this->owner->attributes['birthdate']['Month'];
        $day   = $this->owner->attributes['birthdate']['Day'];
        
        $inputDate = $year.$month.$day;
        $todayDate = date('Ymd');
        
        if ( $inputDate == $todayDate OR ( $inputDate > $todayDate ) )
        {// Дата рождения не может быть сегодняшней или больше текущей
            $this->owner->addError('birthdate', "Не указана дата рождения");
        }
        
        return mktime(12, 0, 0, (int)$month, (int)$day, (int)$year);
    }
}