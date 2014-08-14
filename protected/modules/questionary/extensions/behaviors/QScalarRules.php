<?php

/**
 * Правила проверки для простых полей формы анкеты
 * @todo языковые строки
 * @todo оставить только правила, необходимые для сохранения модели
 *       все остальные правила вынести в модель динамической формы
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
            array('userid, isactor, isamateuractor, hasfilms, isemcee, istvshowmen,
                        isstatist, ismassactor,
                        isparodist, istwin, ismodel, isphotomodel, ispromomodel, isdancer,
                        hasawards, isstripper, issinger,
                        ismusician, issportsman, isextremal, isathlete, hasskills, hastricks,
                        haslanuages, hasinshurancecard, countryid, nativecountryid,
                        shoessize, rating, hastatoo, playagemin, playagemax, 
                        istheatreactor, ismediaactor, ownerid, currentcountryid, visible',
                'numerical', 'integerOnly' => true),
            
            array('firstname, lastname, middlename, city, cityid, inshurancecardnum', 'length', 'max' => 128),
        
            array('mobilephone, homephone, addphone, inn', 'length', 'max' => 32),
            array('status', 'length', 'max' => 50),
            array('hairlength, wearsize', 'length', 'max' => 16),
            array('vkprofile, fbprofile, okprofile, passportorg', 'length', 'max' => 255),
            array('privatecomment, admincomment, moderationcomment', 'length', 'max' => 4095),
            array('passportserial, passportnum', 'length', 'max' => 10),
            
            // @todo добавить индивидуальную проверку для каждого типа поля
            array('passportdate, gender, height, weight, wearsize, looktype, haircolor, eyecolor,
                    physiquetype, titsize, chestsize, waistsize, hipsize, striptype
                    striplevel, singlevel, wearsize', 'safe'),
            // @todo добавить проверку сложных значений или проводить ее асинхронно при работе
            //       с изменяемой grid-таблицей
            array('voicetimbre, addchar, parodist, twin, vocaltype, sporttype, extremaltype, trick, skill', 'safe'),
            // @todo пропустить через trim все остальные поля
            array('formattedBirthDate, firstname, lastname, birthdate, gender, galleryid, city', 
                'filter', 'filter' => 'trim'),
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
            //array('firstname, lastname, gender, formattedBirthDate', 'required', 'on' => 'update'),
            // проверка даты рождения
            //array('formattedBirthDate', 'filter', 'filter' => array($this, 'checkBirthDate')),
            //array('birthdate', 'numerical', 'integerOnly' => true),
        );
    }
    
    /**
     * Фильтр для даты рождения
     * @param int $galleryId
     * @return int
     */
    public function checkBirthDate($date)
    {
        if ( strpos($date, '.') === false OR $this->owner->scenario != 'update' )
        {
            return $date;
        }
        return CDateTimeParser::parse($date, Yii::app()->params['inputDateFormat']);
    }
}