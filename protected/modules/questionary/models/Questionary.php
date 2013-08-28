<?php

/**
 * Модель анкеты участника
 * Таблица "{{questionaries}}".
 *
 * Поля:
 * @property integer $id
 * @property string $userid
 * @property string $mainpictureid
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property integer $birthdate
 * @property string $gender
 * @property string $timecreated
 * @property string $timefilled
 * @property string $timemodified
 * @property string $height
 * @property string $weight
 * @property string $wearsizemin
 * @property string $wearsizemax
 * @property string $shoessize
 * @property string $city
 * @property integer $cityid
 * @property string $mobilephone
 * @property string $homephone
 * @property string $addphone
 * @property string $vkprofile
 * @property integer $looktype
 * @property integer $haircolor
 * @property integer $iscoloredhair
 * @property integer $newhaircolor
 * @property integer $eyecolor
 * @property integer $physiquetype
 * @property integer $isactor
 * @property integer $hasfilms
 * @property integer $isemcee
 * @property integer $isparodist
 * @property integer $istwin
 * @property integer $ismodel
 * @property integer $isphotomodel
 * @property integer $ispromomodel
 * @property string $titsize
 * @property string $chestsize
 * @property string $waistsize
 * @property string $hipsize
 * @property integer $isdancer
 * @property integer $hasawards
 * @property integer $isstripper
 * @property string $striptype
 * @property string $striplevel
 * @property integer $issinger
 * @property string $singlevel
 * @property integer $ismusician
 * @property integer $issportsman
 * @property integer $isextremal
 * @property integer $isathlete
 * @property integer $hasskills
 * @property integer $hastricks
 * @property integer $haslanuages
 * @property integer $wantsbusinesstrips
 * @property string $countryid
 * @property integer $hasinshurancecard
 * @property string $inshurancecardnum
 * @property integer $hasforeignpassport
 * @property string $passportexpires
 * @property string $passportserial
 * @property string $passportnum
 * @property string $passportdate
 * @property string $passportorg
 * @property string $addressid
 * @property integer $policyagreed
 * @property string $status
 * @property integer $encrypted
 * @property integer $rating
 * @property integer $hastatoo
 * 
 * Relations:
 * @property User $user 
 * 
 * Stats:
 * @property int invitesCount
 * @property int requestsCount
 * @property int upcomingEventsCount
 * 
 * @todo вынести преобразование скалярных полей в отдельный behaviour
 * @todo сделать отдельный behaviour для проверок всех типов полей
 * @todo вынести сохранение простых типов деятельности в отдельный behaviour
 * @todo запретить редактировать анкету, если участник уже подал заявку
 */
class Questionary extends CActiveRecord
{
    // размеры одежды "меньше 36" и "больше 56" (как они хранятся в базе данных) 
    const WEARSIZE_MIN = 1;
    const WEARSIZE_MAX = 99;
    
    // размеры обуви "меньше 36" и "больше 45" (как они хранятся в базе данных)
    const SHOESSIZE_MIN = 1;
    const SHOESSIZE_MAX = 99;
    
    // Значение стоящее в select-списках на пункте "выбрать"
    const VALUE_NOT_SET = "";
    
    // отображаемое количество последних приглашений
    const LAST_INVITES_COUNT = 20;
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.questionary.extensions.behaviors.*');
        Yii::import('application.modules.projects.models.*');
        parent::init();
    }
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Questionary the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{questionaries}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('userid,  birthdate, cityid, isactor, isamateuractor, hasfilms, isemcee, istvshowmen,
                    isstatist, ismassactor
                    isparodist, istwin, ismodel, isphotomodel, ispromomodel, isdancer,
                    hasawards, isstripper, issinger,
                    ismusician, issportsman, isextremal, isathlete, hasskills, hastricks, 
                    haslanuages, hasinshurancecard, countryid, nativecountryid,
                    shoessize, passportdate, rating, hastatoo, playagemin, playagemax,
                    istheatreactor, ismediaactor',
                   'numerical', 'integerOnly'=>true),
            array('firstname, lastname, middlename, city, inshurancecardnum', 'length', 'max'=>128),
            
            array('mobilephone, homephone, addphone, inn', 'length', 'max'=>32),
            array('hairlength, wearsize', 'length', 'max'=>16),
            array('vkprofile, fbprofile, okprofile, passportorg, admincomment', 'length', 'max'=>255),
            array('privatecomment', 'length', 'max'=>4095),
            array('passportserial, passportnum', 'length', 'max'=>10),
            
            // @todo добавить индивидуальную проверку для каждого типа поля
            array('gender, height, weight, wearsize, looktype, haircolor, eyecolor,
                    physiquetype, titsize, chestsize, waistsize, hipsize, striptype
                    striplevel, singlevel, wearsize, status', 'safe'),
            
            // @todo добавить проверку сложных значений
            array('voicetimbre, addchar, parodist, twin, vocaltype, sporttype, extremaltype, trick, skill', 'safe'),
            // @todo удалить этот список полей, он не пригодился
            /* array('id, userid, mainpictureid, firstname, lastname, middlename, birthdate, 
                    gender, timecreated, timefilled, timemodified, height, weight, wearsizemin, wearsizemax, 
                    shoessize, city, cityid, mobilephone, homephone, addphone, vkprofile, looktype, haircolor, 
                    eyecolor, physiquetype, isactor, hasfilms, isemcee, isparodist, istwin, ismodel, isphotomodel, ispromomodel, titsize,
                    chestsize, waistsize, hipsize, isdancer, hasawards, isstripper, striptype,
                    striplevel, issinger, singlevel, ismusician, issportsman,
                    isextremal, isathlete, hasskills, hastricks, haslanuages, wantsbusinesstrips,  
                    countryid, hasinshurancecard, hasforeignpassport, passportexpires, wearsize', 'safe', 'on'=>'search'),*/
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // Связи обычных значений с другими таблицами
            // ссылка на пользователя системы
            'user'    => array(self::BELONGS_TO, Yii::app()->getModule('questionary')->userClass, 'userid'),
            // город проживания
            'cityobj' => array(self::BELONGS_TO, 'CSGeoCity', 'cityid'),
            // страна (гражданство)
            'country' => array(self::BELONGS_TO, 'CSGeoCountry', 'countryid'),
            // страна рождения
            'nativecountry' => array(self::BELONGS_TO, 'CSGeoCountry', 'nativecountryid'),
            // домашний адрес (все адреса хранятся в отдельной таблице)
            'address' => array(self::HAS_ONE, 'Address', 'objectid',
                    'condition' => "objecttype='questionary'"),
            // условия участия в съемках
            'recordingconditions' => array(self::HAS_ONE, 'QRecordingConditions', 'questionaryid'),
            
            
            // Сложные значения анкеты, хранящиеся в других таблицах
            
            // Дополнительные характеристики (близнецы, гетерохромия и т. п.)
            'addchars' => array(self::HAS_MANY, 'QAddChar', 'questionaryid'),
            // Законченные актерские ВУЗы
            'actoruniversities' => array(self::HAS_MANY, 'QActorUniversity', 'questionaryid'),
            // Фильмография
            'films' => array(self::HAS_MANY, 'QFilmInstance', 'questionaryid'),
            // Мероприятия ведущего
            'emceelist' => array(self::HAS_MANY, 'QEmcee', 'questionaryid'),
            // образы пародиста
            'parodistlist' => array(self::HAS_MANY, 'QParodist', 'questionaryid'),
            // образы двойника
            'twinlist' => array(self::HAS_MANY, 'QTwin', 'questionaryid'),
            // модельные школы
            'modelschools'=> array(self::HAS_MANY, 'QModelSchool', 'questionaryid'),
            // показы
            'modeljobs' => array(self::HAS_MANY, 'QModelJob', 'questionaryid'),
            // работы в качестве фотомодели
            'photomodeljobs' => array(self::HAS_MANY, 'QPhotoModelJob', 'questionaryid'),
            // мероприятия в качестве промо-модели
            'promomodeljobs' => array(self::HAS_MANY, 'QPromoModelJob', 'questionaryid'),
            // Стили танца
            'dancetypes' => array(self::HAS_MANY, 'QDanceType', 'questionaryid'),
            // призы и награды
            'awards' => array(self::HAS_MANY, 'QAward', 'questionaryid'),
            // типы вокала
            'vocaltypes' => array(self::HAS_MANY, 'QVocalType', 'questionaryid'),
            // тембры голоса
            'voicetimbres' => array(self::HAS_MANY, 'QVoiceTimbre', 'questionaryid'),
            // освоенные музыкальные инструменты
            'instruments' => array(self::HAS_MANY, 'QInstrument', 'questionaryid'),
            // ВУЗы в которых было получено образование по музыкальной специальности или вокалу
            'musicuniversities' => array(self::HAS_MANY, 'QMusicUniversity', 'questionaryid'),
            // владение видами спорта
            'sporttypes' => array(self::HAS_MANY, 'QSportType', 'questionaryid'),
            // экстремальные виды спорта
            'extremaltypes' => array(self::HAS_MANY, 'QExtremalType', 'questionaryid'),
            // выполнение трюков
            'tricks' => array(self::HAS_MANY, 'QTrick', 'questionaryid'),
            // дополнительные умения и навыки
            'skills' => array(self::HAS_MANY, 'QSkill', 'questionaryid'),
            // владение иностранными языками
            'languages' => array(self::HAS_MANY, 'QLanguage', 'questionaryid'),
            // передачи телеведущего
            'tvshows' => array(self::HAS_MANY, 'QTvshowInstance', 'questionaryid'),
            // работа в театре
            'theatres' => array(self::HAS_MANY, 'QTheatreInstance', 'questionaryid'),
            // видео
            'video' => array(self::HAS_MANY, 'Video', 'objectid',
                    'condition' => "objecttype='questionary'"),
            
            // Связи с проектами и мероприятиями
            
            // Новые (еще не просмотренные) приглашения на мероприятия
            'invites' => array(self::HAS_MANY, 'EventInvite', 'questionaryid', 
                'condition' => "`invites`.`status` = 'pending' AND `deleted` = 0",
                'limit'     => self::LAST_INVITES_COUNT),
            // Старые (уже просмотренные) приглашения на мероприятия
            // (все что не в статусе черновик)
            'oldinvites' => array(self::HAS_MANY, 'EventInvite', 'questionaryid', 
                'condition' => "`invites`.`status` != 'draft' `deleted` = 0",
                'limit'     => self::LAST_INVITES_COUNT),
            // Неподтвержденные заявки на участие в мероприятиях
            'requests' => array(self::HAS_MANY, 'MemberRequest', 'memberid'),
            // Предварительно одобренные заявки на участие
            'pendingrequests' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`memberinstances`.`status`='pending'"),
            // Участие во всех мероприятиях
            'memberinstances' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`memberinstances`.`status`='active' OR status='finished'"),
            // Активность в текущих мероприятиях
            'activememberinstances' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`activememberinstances`.`status`='active'"),
            // История участия во всех прошедших мероприятиях
            'finishedmemberinstances' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`finishedmemberinstances`.`status`='finished'"),
            // @todo Проекты, (сделать связь типа "мост" + DISTINCT)
            
            
            // Статистика
            // Неподтвержденные заявки на участие в мероприятиях
            'requestsCount' => array(self::STAT, 'MemberRequest', 'memberid'),
            // Предварительно одобренные заявки на участие
            'pendingRequestsCount' => array(self::HAS_MANY, 'ProjectMember', 'memberid',
                'condition' => "`status`='pending'"),
            // Новые (еще не просмотренные) приглашения на мероприятия
            'invitesCount' => array(self::STAT, 'EventInvite', 'questionaryid',
                'condition' => "`status`='pending' AND `deleted`=0"),
            // Предстоящие съемки (время при выборке не учитываем, они сами завершаться когда нужно)
            'upcomingEventsCount' => array(self::STAT, 'ProjectMember', 'memberid',
                'condition' => "`status`='active'"),
        );
    }

    /**
     * @see parent::behaviors
     */
    public function behaviors()
    {
        Yii::import('ext.galleryManager.*');
        Yii::import('application.modules.questionary.extensions.behaviors.QManageDefaultValuesBehavior');
        
        // получаем настройки галереи изображений из модуля
        $gallerySettings = Yii::app()->getModule('questionary')->gallerySettings;
        // @todo Устанавливаем директорию, чтобы все изображения пользователя хранились в отдельной папке
        
        return array(
            // сохранение и получение скалярных полей анкеты
            'QManageScalarValueBehavior' => array(
                'class' => 'application.modules.questionary.extensions.behaviors.QManageScalarValueBehavior',
            ),
            'QManageDefaultValuesBehavior',
                array('class' => 'application.modules.questionary.extensions.behaviors.QManageDefaultValuesBehavior'),
            // подключаем behavior для загрузки изображений в анкету актера
            'galleryBehavior' => $gallerySettings,
            // автоматическое заполнение дат создания и изменения
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'timecreated',
                'updateAttribute' => 'timemodified',
            )
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::beforeSave()
     */
    protected function beforeSave()
    {
        if ( $this->isNewRecord )
        {
            $this->status = 'draft';
        }else
        {
           if ( PHP_SAPI == 'cli' )
           {// @todo это хак, позволяющий обойти ошибку, возникающую при попытке обновить модель
               // анкеты при запуске миграции из командной строки. Нужно переписать эти проверки так, чтобы
               // ими можно было управлять при помощи параметра needsValidation в save()
               return parent::beforeSave();
           }
           // Автоматически модерируем анкету, исправляя то, что можем исправить
           $this->autoModeration();
           // после этого проверяем, нужна ли анкете ручная модерация.
           // Для этого берем старые и новые значения из анкеты и сравниваем их
           if ( $this->needsModerating() )
           {// если изменились какие-то ключевые поля, то сохраненная пользователем анкета 
               // сначала должна пройти проверку модератором
               $this->status = 'pending';
           }
           // Удаляем комментарий администрации к анкете, если она уже была исправлена
           $this->deleteAdminComment();
           
           // Отправляем участнику сообщение о том, что его анкета заполнена (если нужно)
           $this->sendAdminFillingNotification();
        }
        
        return parent::beforeSave();
    }
    
    /**
     * @see CActiveRecord::afterSave()
     * @return null
     */
    protected function afterSave()
    {
        if ( ! $this->address )
        {// при создании новой анкеты автоматически создаем запись с адресом
            $address = new Address('questionary');
            $address->objectid = $this->id;
            $address->save();
        }
        
        if ( ! $this->recordingconditions )
        {// и с условиями участия в съемках
            $recordingConditions = new QRecordingConditions();
            $recordingConditions->questionaryid = $this->id;
            $recordingConditions->save();
        }
        
        parent::afterSave();
    }
    
    /**
     * @todo при рефакторинге переписать удаление через события. Слушать дочерними объектами родительский
     * (non-PHPdoc)
     * @see CActiveRecord::beforeDelete()
     */
    protected function beforeDelete()
    {
        if ( $this->address )
        {// удаляем адрес
            $this->address->delete();
        }
        
        if ( $this->recordingconditions )
        {// удаляем условия съемки
            $this->recordingconditions->delete();
        }
        
        // подключаем модели из других модулей, чтобы все корректно удалилось
        Yii::import('application.modules.projects.models.*');
        
        // удаляем все связанные с анкетой данные в других таблицах
        $relations = array('memberinstances', 'requests', 'invites', 'tvshows', 'languages', 'skills', 'tricks',
            'extremaltypes', 'sporttypes', 'musicuniversities', 'instruments', 'voicetimbres', 'vocaltypes', 
            'awards', 'dancetypes', 'promomodeljobs', 'photomodeljobs', 'modeljobs', 'modeljobs', 'modelschools',
            'twinlist', 'parodistlist', 'emceelist', 'films', 'actoruniversities', 'addchars');
        
        foreach ( $relations as $data )
        {
            foreach ( $this->$data as $object )
            {
                $object->delete();
            }
        }
        
        return parent::beforeDelete();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'userid' => QuestionaryModule::t('userid_label'),
            'firstname' => QuestionaryModule::t('firstname_label'),
            'lastname' => QuestionaryModule::t('lastname_label'),
            'middlename' => QuestionaryModule::t('middlename_label'),
            'birthdate' => QuestionaryModule::t('birthdate_label'),
            'gender' => QuestionaryModule::t('gender_label'),
            'timecreated' => QuestionaryModule::t('timecreated_label'),
            'timefilled' => QuestionaryModule::t('timefilled_label'),
            'timemodified' => QuestionaryModule::t('timemodified_label'),
            'height' => QuestionaryModule::t('height_label'),
            'weight' => QuestionaryModule::t('weight_label'),
            'shoessize' => QuestionaryModule::t('shoessize_label'),
            'city' => QuestionaryModule::t('city_label'),
            'cityid' => QuestionaryModule::t('city_label'),
            'mobilephone' => QuestionaryModule::t('mobilephone_label'),
            'homephone' => QuestionaryModule::t('homephone_label'),
            'addphone' => QuestionaryModule::t('addphone_label'),
            'vkprofile' => QuestionaryModule::t('vkprofile_label'),
            'fbprofile' => QuestionaryModule::t('fbprofile_label'),
            'okprofile' => QuestionaryModule::t('okprofile_label'),
            'looktype' => QuestionaryModule::t('looktype_label'),
            'haircolor' => QuestionaryModule::t('haircolor_label'),
            'iscoloredhair' => QuestionaryModule::t('iscoloredhair_label'),
            'newhaircolor' => QuestionaryModule::t('newhaircolor_label'),
            'eyecolor' => QuestionaryModule::t('eyecolor_label'),
            'physiquetype' => QuestionaryModule::t('physiquetype_label'),
            'isactor' => QuestionaryModule::t('isactor_label'),
            'hasfilms' => QuestionaryModule::t('hasfilms_label'),
            'isemcee' => QuestionaryModule::t('isemcee_label'),
            'isparodist' => QuestionaryModule::t('isparodist_label'),
            'istwin' => QuestionaryModule::t('istwin_label'),
            'ismodel' => QuestionaryModule::t('ismodel_label'),
            'isphotomodel' => QuestionaryModule::t('isphotomodel_label'),
            'ispromomodel' => QuestionaryModule::t('ispromomodel_label'),
            'titsize' => QuestionaryModule::t('titsize_label'),
            'chestsize' => QuestionaryModule::t('chestsize_label'),
            'waistsize' => QuestionaryModule::t('waistsize_label'),
            'hipsize' => QuestionaryModule::t('hipsize_label'),
            'isdancer' => QuestionaryModule::t('isdancer_label'),
            'hasawards' => QuestionaryModule::t('hasawards_label'),
            'isstripper' => QuestionaryModule::t('isstripper_label'),
            'striptype' => QuestionaryModule::t('striptype_label'),
            'striplevel' => QuestionaryModule::t('striplevel_label'),
            'issinger' => QuestionaryModule::t('issinger_label'),
            'singlevel' => QuestionaryModule::t('singlevel_label'),
            'voicetimbre' => QuestionaryModule::t('voicetimbre_label'),
            'ismusician' => QuestionaryModule::t('ismusician_label'),
            'issportsman' => QuestionaryModule::t('issportsman_label'),
            'isextremal' => QuestionaryModule::t('isextremal_label'),
            'isathlete' => QuestionaryModule::t('isathlete_label'),
            'hasskills' => QuestionaryModule::t('hasskills_label'),
            'hastricks' => QuestionaryModule::t('hastricks_label'),
            'haslanuages' => QuestionaryModule::t('haslanuages_label'),
            'wantsbusinesstrips' => QuestionaryModule::t('wantsbusinesstrips_label'),
            'country' => QuestionaryModule::t('country_label'),
            'countryid' => QuestionaryModule::t('country_label'),
            'hasinshurancecard' => QuestionaryModule::t('hasinshurancecard_label'),
            'inshurancecardnum' => QuestionaryModule::t('inshurancecardnum_label'),
            'hasforeignpassport' => QuestionaryModule::t('hasforeignpassport_label'),
            'passportexpires' => QuestionaryModule::t('passportexpires_label'),
            'passportserial' => QuestionaryModule::t('passportserial_label'),
            'passportnum' => QuestionaryModule::t('passportnum_label'),
            'passportdate' => QuestionaryModule::t('passportdate_label'),
            'passportorg' => QuestionaryModule::t('passportorg_label'),
            'policyagreed' => QuestionaryModule::t('policyagreed_label'),
            'status' => QuestionaryModule::t('status_label'),
            'encrypted' => QuestionaryModule::t('encrypted_label'),
            'rating' => QuestionaryModule::t('rating_label'),
            'salary' => QuestionaryModule::t('salary_label'),
            'hastatoo' => QuestionaryModule::t('hastatoo_label'),
            'istvshowmen' => QuestionaryModule::t('istvshowmen_label'),
            'isamateuractor' => QuestionaryModule::t('isamateuractor_label'),
            'isstatist' => QuestionaryModule::t('isstatist_label'),
            'ismassactor' => QuestionaryModule::t('ismassactor_label'),
            'nativecountryid' => QuestionaryModule::t('nativecountryid_label'),
            'admincomment' => QuestionaryModule::t('admincomment_label'),
            'inn' => QuestionaryModule::t('inn_label'),
            'playage' => QuestionaryModule::t('playage_label'),
            'playagemin' => QuestionaryModule::t('playagemin_label'),
            'playagemax' => QuestionaryModule::t('playagemax_label'),
            'facetype' => QuestionaryModule::t('facetype_label'),
            'hairlength' => QuestionaryModule::t('hairlength_label'),
            'canrepainthair' => QuestionaryModule::t('canrepainthair_label'),
            'istheatreactor' => QuestionaryModule::t('istheatreactor_label'),
            'ismediaactor' => QuestionaryModule::t('ismediaactor_label'),
            'privatecomment' => QuestionaryModule::t('privatecomment_label'),

            // пояснения, не являющиеся полями
            'level' => QuestionaryModule::t('level'),
            'type' => QuestionaryModule::t('type'),
            'passport' => QuestionaryModule::t('passport_data'),
            'wearsize' => QuestionaryModule::t('wearsize_label'),
            'address' => QuestionaryModule::t('address'),
            
            // сложные поля, хранящиеся в других таблицах
            'addchar' => QuestionaryModule::t('addchar_label'),
            'university' => QuestionaryModule::t('universities_label'),
            'sporttype' => QuestionaryModule::t('sporttype_label'),
            'extremaltype' => QuestionaryModule::t('extremaltype_label'),
            'skill' => QuestionaryModule::t('skill_label'),
            'vocaltype' => QuestionaryModule::t('vocaltype_label'),
            'photos' => QuestionaryModule::t('photos_label'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     * 
     * @todo удалить при рефакторинге если не понадобится
     */
    /*public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('firstname',$this->firstname,true);
        $criteria->compare('lastname',$this->lastname,true);
        $criteria->compare('middlename',$this->middlename,true);
        $criteria->compare('birthdate',$this->birthdate);
        $criteria->compare('gender',$this->gender,true);
        $criteria->compare('timecreated',$this->timecreated,true);
        $criteria->compare('timefilled',$this->timefilled,true);
        $criteria->compare('timemodified',$this->timemodified,true);
        $criteria->compare('height',$this->height,true);
        $criteria->compare('weight',$this->weight,true);
        $criteria->compare('wearsizemin',$this->wearsizemin,true);
        $criteria->compare('wearsizemax',$this->wearsizemax,true);
        $criteria->compare('shoessize',$this->shoessize,true);
        $criteria->compare('city',$this->city,true);
        $criteria->compare('cityid',$this->city);
        $criteria->compare('mobilephone',$this->mobilephone,true);
        $criteria->compare('homephone',$this->homephone,true);
        $criteria->compare('addphone',$this->addphone,true);
        $criteria->compare('vkprofile',$this->vkprofile,true);
        $criteria->compare('looktype',$this->looktype);
        $criteria->compare('haircolor',$this->haircolor);
        $criteria->compare('iscoloredhair',$this->iscoloredhair);
        $criteria->compare('eyecolor',$this->eyecolor);
        $criteria->compare('physiquetype',$this->physiquetype);
        $criteria->compare('isactor',$this->isactor);
        $criteria->compare('hasfilms',$this->hasfilms);
        $criteria->compare('isemcee',$this->isemcee);
        $criteria->compare('isparodist',$this->isparodist);
        $criteria->compare('istwin',$this->istwin);
        $criteria->compare('ismodel',$this->ismodel);
        $criteria->compare('titsize',$this->titsize,true);
        $criteria->compare('chestsize',$this->chestsize,true);
        $criteria->compare('waistsize',$this->waistsize,true);
        $criteria->compare('hipsize',$this->hipsize,true);
        $criteria->compare('isdancer',$this->isdancer);
        $criteria->compare('hasawards',$this->hasawards);
        $criteria->compare('isstripper',$this->isstripper);
        $criteria->compare('striptype',$this->striptype,true);
        $criteria->compare('striplevel',$this->striplevel,true);
        $criteria->compare('issinger',$this->issinger);
        $criteria->compare('singlevel',$this->singlevel,true);
        $criteria->compare('ismusician',$this->ismusician);

        $criteria->compare('issportsman',$this->issportsman);
        $criteria->compare('isextremal',$this->isextremal);
        $criteria->compare('isathlete',$this->isathlete);
        $criteria->compare('hasskills',$this->hasskills);
        $criteria->compare('hastricks',$this->hastricks);
        $criteria->compare('haslanuages',$this->haslanuages);
        $criteria->compare('wantsbusinesstrips',$this->wantsbusinesstrips);
        $criteria->compare('countryid',$this->countryid,true);
        $criteria->compare('hasinshurancecard',$this->hasinshurancecard);
        $criteria->compare('inshurancecardnum',$this->inshurancecardnum,true);
        $criteria->compare('hasforeignpassport',$this->hasforeignpassport);
        $criteria->compare('passportexpires',$this->passportexpires,true);
        $criteria->compare('passportserial',$this->passportserial,true);
        $criteria->compare('passportnum',$this->passportnum,true);
        $criteria->compare('passportdate',$this->passportdate,true);
        $criteria->compare('passportorg',$this->passportorg,true);
        $criteria->compare('addressid',$this->addressid,true);
        $criteria->compare('policyagreed',$this->policyagreed);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('encrypted',$this->encrypted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }*/

    /**
     * Получить список достижений и умений участника: спортсмен, актер, атлет, и т. д.
     * @return array 
     */
    public function getBages()
    {
        $bages = array();
        
        // актер (актриса)
        if ( $this->isactor )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('actor');
            }else
           {
               $bages[] = QuestionaryModule::t('actress');
            }
        }
        // непрофессиональный актер
        if ( $this->isamateuractor )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('amateuractor(male)');
            }else
           {
                $bages[] = QuestionaryModule::t('amateuractor(female)');
            }
        }
        // статист
        if ( $this->isstatist )
        {
            $bages[] = QuestionaryModule::t('statist');
        }
        // актер массовых сцен
        if ( $this->ismassactor )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('massactor(male)');
            }else
           {
                $bages[] = QuestionaryModule::t('massactor(female)');
            }
        }
        // ведущий (ведущая)
        if ( $this->isemcee )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('emcee(male)');
            }else
           {
                $bages[] = QuestionaryModule::t('emcee(female)');
            }
        }
        // телеведущий
        if ( $this->istvshowmen )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('tvshowmen(male)');
            }else
            {
                $bages[] = QuestionaryModule::t('tvshowmen(female)');
            }
        }
        // пародист
        if ( $this->isparodist )
        {
            $bages[] = QuestionaryModule::t('parodist');
        }
        // двойник
        if ( $this->istwin )
        {
            $bages[] = QuestionaryModule::t('twin');
        }
        // модель
        if ( $this->ismodel )
        {
            $bages[] = QuestionaryModule::t('model');
        }
        // фотомодель
        if ( $this->isphotomodel )
        {
            $bages[] = QuestionaryModule::t('photomodel');
        }
        // промо-модель
        if ( $this->ispromomodel )
        {
            $bages[] = QuestionaryModule::t('promomodel');
        }
        // танцор
        if ( $this->isdancer )
        {
            $bages[] = QuestionaryModule::t('dancer');
        }
        // стриптиз
        if ( $this->isstripper )
        {
            $bages[] = QuestionaryModule::t('stripper');
        }
        // вокал
        if ( $this->issinger )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('singer(male)');
            }else
           {
                $bages[] = QuestionaryModule::t('singer(female)');
            }
        }
        // музыкант
        if ( $this->ismusician )
        {
            $bages[] = QuestionaryModule::t('musician');
        }
        // каскадер
        if ( $this->hastricks )
        {
            $bages[] = QuestionaryModule::t('hastricks');
        }
        // спортсмен(ка)
        if ( $this->issportsman )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('sportsman(male)');
            }else
           {
                $bages[] = QuestionaryModule::t('sportsman(female)');
            }
        }
        // экстремал
        if ( $this->isextremal )
        {
            $bages[] = QuestionaryModule::t('extremal');
        }
        // атлет
        if ( $this->isathlete )
        {
            $bages[] = QuestionaryModule::t('athlete');
        }
        // актер театра
        if ( $this->istheatreactor )
        {
            if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('theatreactor(male)');
            }else
           {
                $bages[] = QuestionaryModule::t('theatreactor(female)');
            }
        }
        // медийный актер
        if ( $this->ismediaactor )
        {
        if ( ! $this->gender OR $this->gender == 'male' )
            {
                $bages[] = QuestionaryModule::t('mediaactor(male)');
            }else
           {
                $bages[] = QuestionaryModule::t('mediaactor(female)');
            }
        }
        
        asort($bages);
        return $bages;
    }
    
    /**
     * Автоматическая премодарация анкеты. Помогает администраторам.
     * 
     * ВАЖНО!
     * При запуске этой функции мы полагаемся на то, что все сложные значения анкеты уже сохранены в других таблицах,
     * а сама анкета еще не сохранена.
     * 
     * Автоматически сбрасывает галочки умений в анкете если не выполняются определенные условия.
     * Участнику не засчитывается:
     * - профессиональный актер без ВУЗов
     * - фильмография без фильмов
     * - ведущий и телеведущий без мероприятий
     * - Пародист и двойник без образов
     * - Танцор без стилей танца
     * - Певец без музыкального ВУЗа всегда считается любителем
     * 
     * @return null
     */
    protected function autoModeration()
    {
        $dependences = array(
            'isactor' => 'actoruniversities',
            'hasfilms' => 'films',
            'isemcee' => 'emceelist',
            'istvshowmen' => 'tvshows',
            'isparodist' => 'parodistlist',
            'istwin' => 'twinlist',
            'isdancer' => 'dancetypes',
            'haslanuages' => 'languages',
            'hasawards' => 'awards',
            'istheatreactor' => 'theatres',
        );
        
        foreach ( $dependences as $field => $relation )
        {
            // Перезагружаем связаные записи чтобы не наткнуться на кеш
            $data = $this->getRelated($relation, true);
            if ( isset($this->$field) AND $this->$field AND ! $data )
            {// галочка выставлена, а необходимых данных нет - сбросим ее обратно
                $this->$field = 0;
            }
        }
        
        if ( $this->isactor )
        {// если участник - профессиональный актер - уберем галочку "непрофессиональный"
            $this->isamateuractor = 0;
        }
    }
    
    /**
     * Определить, должна ли анкета попасть к модератору после сохранения пользователем
     * Анкета отправляется к модератору только если были включены любые поля, отвечающие за умения
     * (например спортсмен, музыкант, вокал, и т. п.)
     * Исключение - актер массовых сцен и статист - эти поля могут быть указаны кем угодно и не требуют проверки
     * модератором
     * @todo переделать проверку прав
     * 
     * @return bool
     */
    protected function needsModerating()
    {
        // старые данные анкеты
        $old = $this::model()->findByPk($this->id);
        // новые данные анкеты
        $new = $this;
        
        if ( Yii::app()->user->checkAccess('Admin') )
        {// админы и модераторы не нуждаются ни в каких проверках - их анкеты совершенно другие
            // также, если кто-то из них сохраняет анкету - значит он знает что делает
            return false;
        }
        
        if ( $this->status == 'draft' )
        {// первое заполнение анкеты всегда требует модерации (только если ее не завел админ)
            return true;
        }
        
        $fields = array('isactor', 'hasfilms', 'isemcee', 'isparodist', 'istwin', 'ismodel', 'isdancer', 
            'hasawards', 'isstripper', 'issinger', 'ismusician',  'issportsman',  'isextremal',  'isathlete', 
            'hastricks',  'hasskills',  /*'haslanuages',*/ 'isphotomodel', 'ispromomodel', 'isamateuractor',
            'istvshowmen');
        
        foreach ( $fields as $field )
        {
            if ( isset($new->$field) AND ! $old->$field AND $new->$field )
            {// если галочка до этого не стояла, а потом была отмечена - значит анкету должен просмотреть модератор
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Удалить комментарий администрации (если анкета одобрена, переведена в статус ожидания проверки 
     * или перешла из состояния "отложена")
     * @todo перенести эту логику в функцию setStatus при рефакторинге
     * 
     * @return null
     */
    protected function deleteAdminComment()
    {
        // старые данные анкеты
        $old = $this::model()->findByPk($this->id);
        // новые данные анкеты
        $new = $this;
        if ( $old->status == 'delayed' AND $old->status != $new->status )
        {
            $this->admincomment = '';
            return;
        }
        if ( $new->status == 'active' )
        {
            $this->admincomment = '';
            return;
        }
        if ( $new->status == 'pending' AND $old->status != $new->status )
        {
            $this->admincomment = '';
            return;
        }
    }
    
    /**
     * Отослать участнику письмо о том что его анкета заполнена, с просьбой проверить данные, 
     * которые ввел наш администратор
     * (если анкета заводилась нами вручную)
     * Эта функция должна вызываться перед сохранением данных анкеты
     * 
     * @todo языковые строки
     * @todo присылать разные сообщения для участника, заказчика и члена команды
     * 
     * @return null
     */
    protected function sendAdminFillingNotification()
    {
        $filled    = false;
        $user      = $this->user;
        // старый статус анкеты
        $oldStatus = self::model()->findByPk($this->id)->status;
        // новый статус анкеты
        $newStatus = $this->status;
        // URL для активации учетной записи
        $activationUrl = Yii::app()->createAbsoluteUrl(
                '/user/activation/activation',
                array("activkey" => $user->activkey, "email" => $user->email)
            );
        
        // ссылка на просмотр анкеты
        $questionaryUrl = Yii::app()->createAbsoluteUrl(
                Yii::app()->getModule('questionary')->profileUrl,
                array("id" => $this->id)
            );
        $questionaryUrl = CHtml::link($questionaryUrl, $questionaryUrl);
        
        if ( $oldStatus == 'draft' AND $newStatus == 'active' )
        {// если анкета только что перешла из статуса "черновик" в активный статус - 
            // значит пользователя только что зарегистрировали и ввели все данные
            $filled = true;
        }
        
        if ( ! Yii::app()->user->isSuperuser OR ! $filled )
        {// Эта функция используется только если анкету заполняет админ и только при первом сохранении анкеты
            return;
        }
        
        // Тема и текст письма
        $theme = 'Ввод данных завершен';
        $message = 'Добрый день.
                Мы закончили создание вашей анкеты. Пожалуйства проверьте правильность введенных нами данных.';
        $message .= "<br><br>";
        $message .= "Вы можете просмотреть свою анкету по адресу: ".$questionaryUrl;
        $message .= "<br><br>";
        $message .= "<br><br>";
        $message .= UserModule::t("Please activate you account go to {activation_url}",
                array('{activation_url}'=>$activationUrl)
            );
        $message .= "<br><br>";
        $message .= "С уважением, команда проекта EasyCast";
        
        // отсылаем письмо
        UserModule::sendMail($user->email, $theme, $message);
        // запоминаем кто ввел анкету
        $history = new QCreationHistory();
        $history->userid = Yii::app()->user->id;
        $history->questionaryid = $this->id;
        $history->timecreated = time();
        $history->save();
    }
    
    /**
     * Определить, впервый ли раз сохраняется анкета
     * Анкета, которая выходит из статуса "отложена" тоже считается первый раз сохраненной
     * При вызове этой функции мы считаем, что данные еще не сохранены. Ее следует вызывать из beforeSave
     * @todo запретить обращение к функции при isNewRecord=true
     * 
     * @return bool
     */
    public function isFirstSave()
    {
        $old = $this::model()->findByPk($this->id);
        $new = $this;
        if ( ! $this->timemodified )
        {// анкета еще ни разу не сохранялась
            return true;
        }
        if ( $old->status == 'delayed' AND $old->status != $new->status )
        {// анкета перестает быть отложеной
            return true;
        }
        if ( $old->status == 'draft' AND $new->status != 'delayed' )
        {// сохраняется черновик анкеты, и анкета не откладывается администратором чтобы заполнить позже
            return true;
        }
        
        return false;
    }
    
    /////////////////////////////////////////////////
    // Сохранение и получение сложных полей анкеты //
    /////////////////////////////////////////////////
    
    /**
     * Сохранить значение простого вида деятельности (у которого нет дополнительных параметров)
     * Например: вид спорта, тембр голоса, и т. п.
     * 
     * @param string $type - тип деятельности в таблице activityTypes
     * @param array $options - значения, содержащиеся внутри поля
     */
    protected function saveSimpleActivity($type, $options)
    {
        if ( ! QActivity::model()->valueIsChanged($this->id, $type, $options) )
        {// значение не изменилось - ничего не делаем
            return;
        }
        
        // проверяем, были ли удалены какие-то из пользовательских значений
        $oldUserValues = QActivity::model()->getFieldValues($this->id, $type, 'user');
        foreach ( $oldUserValues as $value )
        {
            if ( ! in_array($value->id, $options) )
            {// старого пользовательского значения нет в списке переданном из формы - удаляем его
                QActivity::model()->deleteByPk($value->id);
            }
        }
        
        // проверяем, были ли удалены какие-то из стандартных значений
        $oldStandardValues = QActivity::model()->getFieldValues($this->id, $type, 'standard');
        foreach ( $oldStandardValues as $value )
        {
            if ( ! in_array($value->value, $options) )
            {// старого пользовательского значения нет в списке переданном из формы - удаляем его
                QActivity::model()->deleteByPk($value->id);
            }
        }
        
        // проверяем все новые значения сравниваем их со старыми и добавляем, если необходимо
        foreach ( $options as $option )
        {
            if ( is_numeric($option) AND $option )
            {// пользовательское значение, сохраненное в прошлый раз - пропускаем
                continue;
            }
            if ( $this->isDefaultValue($type, $option) )
            {// стандартное значение
                if ( QActivity::model()->valueIsExists($this->id, $type, $option) )
                {// значение уже существует - не добавляем его
                    continue;
                }else
              {// добавлено новое значение = записываем его в БД
                    $activity = new QActivity();
                    $activity->questionaryid = $this->id;
                    $activity->type          = $type;
                    $activity->value         = $option;
                    $activity->save();
                }
            }else
           {// введенное пользователем значение
                $activity = new QActivity();
                $activity->questionaryid = $this->id;
                $activity->type          = $type;
                $activity->value         = 'custom';
                $activity->uservalue     = $option;
                $activity->save();
            }
        }
    }
    
    // Функции сохранения простых видов деятельности
    // (это значения, которые принадлежат анкете, но хранятся в таблице "q_activities")
    // @todo вынести в отдельный behavior
    
    /**
     * Сохранить тембры голоса, которыми владеет пользователь
     * @param array $values
     */
    public function setvoicetimbre($values)
    {
        $this->saveSimpleActivity('voicetimbre', $values);
    }
    
    /**
     * Дополнительные характеристики
     * @param array $values - данные из сложного поля формы
     */
    public function setaddchar($values)
    {
        $this->saveSimpleActivity('addchar', $values);
    }
    
    /**
     * Образы пародиста
     * @param array $values - данные из сложного поля формы
     */
    public function setparodist($values)
    {
        $this->saveSimpleActivity('parodist', $values);
    }
    
    /**
     * Образы двойника
     * @param array $values - данные из сложного поля формы
     */
    public function settwin($values)
    {
        $this->saveSimpleActivity('twin', $values);
    }
    
    /**
     * Типы вокала
     * @param array $values - данные из сложного поля формы
     */
    public function setvocaltype($values)
    {
        $this->saveSimpleActivity('vocaltype', $values);
    }
    
    /**
     * Виды спорта
     * @param array $values - данные из сложного поля формы
     */
    public function setsporttype($values)
    {
        $this->saveSimpleActivity('sporttype', $values);
    }
    
    /**
     * Экстремальные виды спорта
     * @param array $values - данные из сложного поля формы
     */
    public function setextremaltype($values)
    {
        $this->saveSimpleActivity('extremaltype', $values);
    }
    
    /**
     * Выполнение трюков
     * @param array $values - данные из сложного поля формы
     */
    public function settrick($values)
    {
        $this->saveSimpleActivity('trick', $values);
    }
    
    /**
     * Умения и навыки
     * @param array $values - данные из сложного поля формы
     */
    public function setskill($values)
    {
        $this->saveSimpleActivity('skill', $values);
    }
    
    /**
     * Иностранные языки
     * @param array $values - данные из сложного поля формы
     */
    public function setlanguage($values)
    {
        $this->saveSimpleActivity('language', $values);
    }
    
    // преобразования скалярных полей формы
    
    /** Получить список полей, которые нуждаются в преобразовании после прихода из формы
     * 
     * @return multitype:string
     */
    public function convertedFields()
    {
        return array('birthdate', 'passportexpires', 'passportdate');
    }
    
    /** Получить массив атрибутов модели, пригодных для записи в базу
     * @todo посмотреть, можно ли сделдать это нормальным способом через фильтры
     * 
     * @param array $attributes - пришедшие из $_POST данные
     */
    public function getConvertedAttributes($attributes)
    {
        $result = array();
        $needsConversion = $this->convertedFields();
        
        foreach ( $attributes as $name => $value )
        {
            if ( in_array($name, $needsConversion) )
            {// поле нужно преобразовать
                $result = $this->convertFieldValue($result, $name, $value);
            }else
          {
                $result[$name] = $value;
            }
        }
        
        return $result;
    }
    
    /** Преобразовать значение из формы в значение хранимое в базе
     * 
     * @param array $result - массив с итоговым результатом полей
     * @param string $field - поле формы из которого пришли данные
     * @param string|int|array $value - значение поля
     * 
     * @return mixed 
     */
    protected function convertFieldValue($result, $field, $value)
    {
        switch ($field)
        {
            case 'birthdate':       return $this->convertDate($result, $field, $value); break;
            case 'passportexpires': return $this->convertDate($result, $field, $value); break;
            case 'passportdate':    return $this->convertDate($result, $field, $value); break;
            
            default: return $result;
        }
    }
    
    /** Преобразовать значения полей даты из массива в unixtime
     * 
     * @param array $result
     * @param array $field
     * @param array $value
     */
    protected function convertDate($result, $field, $value)
    {
        $result[$field] = ActiveDateSelect::make_unixtime($value);
        
        return $result;
    }
    
    /**
     * Получить список значений по умолчанию для сложного поля
     * (эти поля хранятся во внешних таблицах) 
     * @param string $field
     */
    public function getComplexFieldVariants($field)
    {
        return $this->activityVariants($field);
    }
    
    /**
     * Получить значение одного стандартного варианта сложного поля
     * @param string $type
     * @param string $value
     */
    public function getStandardComplexValue($type, $value)
    {
        $variants = $this->getComplexFieldVariants($type);
        if ( ! isset($variants[$value]) )
        {
            throw new CException(get_class($this).' getStandardComplexValue($type, $value) - invalid $value paramener: "'.$value.'"');
        }
        return $variants[$value];
    }
    
    /**
     * определить, является ли переданный экземпляр сложного значения стандартным
     * @param unknown_type $type
     * @param unknown_type $value
     */
    public function isStandardComplexValue($type, $value)
    {
        $variants = $this->getComplexFieldVariants($type);
	    if ( ! isset($variants[$value]) )
	    {
	        return false;
	    }
	    return true;
    }
    
    /**
     * Получить список вариантов для одного вида деятельности (для использования в select-элементах)
     * @param string $name - тип деятельности (вид спорта, вокал, и т. п.)
     */
    protected function activityVariants($name)
    {
        return QActivityType::model()->activityVariants($name);
    }
    
    /**
     * Определить, отображать по умолчанию указанное поле формы (или фрагмент из нескольких полей)
     * 
     * @param string $section - поле формы или название набора полей
     * @return bool
     */
    public function isDisplayedSection($section)
    {
        $result = true;
        
        // Большинство полей нужно свернуть или скрыть, если в соответствующем поле
        // выбран пункт "нет", поэтому составим карту того, какие поля и разделы от каких галочек зависят
        // Ключами массива являются поля или секции полей (которые нужно показать или скрыть)
        $dependences = array(
            // Театральные ВУЗы (скрыто, если не выбрано "профессиональный актер")
            'actoruniversities' => 'isactor',
            // Фильмография (скрыто, если не указано что пользователь снимался в фильмах)
            'films' => 'hasfilms',
            // Ведущий (скрыто, если не выставлена галочка)
            'emceelist' => 'isemcee',
            // Телеведущий (скрыто без галочки)
            'tvshows' => 'istvshowmen',
            // Пародист (скрыто, если не выставлена галочка)
            'parodist' => 'isparodist',
            // Двойник (скрыто, если не выставлена галочка)
            'twin' => 'istwin',
            // Модельные школы (скрыто, если нет галочки "модель")
            'modelschools' => 'ismodel',
            // Показы (скрыто, если нет галочки "модель")
            'modeljobs' => 'ismodel',
            // Работа фотомоделью (скрыто, если нет галочки)
            'photomodeljobs' => 'isphotomodel',
            // Работа промо-моделью (скрыто, если нет галочки)
            'promomodeljobs' => 'ispromomodel',
            // Стили танца (скрыто, если нет галочки "танцор")
            'dancetypes' => 'isdancer',
            // Тип и уровень стриптиза (Только если выбран "стриптиз")
            'stripdata' => 'isstripper',
            // Типы вокала (скрыто, если не выбран "вокал")
            'vocaltypes' => 'issinger',
            // Тембр голоса (скрыто, если не выбран "вокал")
            'voicetimbres' => 'issinger',
            // Уровень вокала (скрыто, если не выбран "вокал")
            'singlevel' => 'issinger',
            // Музыкальные инструменты (Только если выбран "музыкант")
            'instruments' => 'ismusician',
            // Виды спорта (Скрыто, если не выбран "спортсмен")
            'sporttypes' => 'issportsman',
            // Экстремальные виды спорта (скрыто, если не выбрано "экстремал")
            'extremaltypes' => 'isextremal',
            // Список дополнительных умений и навыков (скрыто, если нет галочки)
            'skills' => 'hasskills',
            // Список трюков (скрыто, если нет галочки)
            'tricks' => 'hastricks',
            // Список иностранных языков (скрыто, если нет галочки)
            'languages' => 'haslanuages',
            // Номер страхового свидетельства (скрыто, если нет свидетельства)
            'inshurancecardnum' => 'hasinshurancecard',
            // Срок действия загранпаспорта (скрыто, если его нет)
            'passportexpires' => 'hasforeignpassport',
            // Список наград (скрыто, если нет галочки)
            'awards' => 'hasawards',
            // Опыт работы в театре
            'actortheatres' => 'istheatreactor',
        );
        
        if ( isset($dependences[$section]) )
        {// Если отображение поля зависит от галочки да/нет - то просто проверим в каком она состоянии
            $modelfield = $dependences[$section];
            $result = (bool)$this->$modelfield;
        }
        
        // Только несколько полей формы имеют более сложные зависимости при отображении. Зададим их.
        switch ( $section )
        {
            // Дополнительные характеристики (свернуто, если нет ни одной)
            case 'addchars':
                if ( ! $this->addchars )
                {
                    $result = false;
                }
            break;
            // Размер груди (скрыто, если выбран мужской пол)
            case 'titsize':
                if ( $this->gender == 'male' )
                {
                    $result = false;
                }
            break;
            // Музыкальные ВУЗы (скрывается, если не выбран ни "музыкант", ни "вокал". Показывается, если 
            // выбрана хотя бы одна галочка)
            case 'musicuniversities':
                if ( ! $this->issinger AND ! $this->ismusician )
                {
                    $result = false;
                }
            break;
            // Фильмография (свернуто, если не выбрано "актер" (профессиональный или непрофессиональный))
            case 'films_part':
                if ( ! $this->isactor AND ! $this->isamateuractor )
                {
                    $result = false;
                }
            break;
            // непрофессиональный актер (скрыто, если выбран "профессиональный актер")
            case 'amateuractor':
                if ( $this->isactor )
                {
                    $result = false;
                }
            break;
        }
        
        return $result;
    }
    
    /**
     * Получить ссылку на картинку с аватаром пользователя
     * 
     * @return string - url картинки или пустая строка, если у пользователя нет аватара
     */
    public function getAvatarUrl($size='small')
    {
        $nophoto = Yii::app()->getModule('questionary')->_assetsUrl.'/images/nophoto.png';
        if ( ! $avatar = $this->getGalleryCover() )
        {// пользователь еще не загрузил аватар
            return $nophoto;
        }
        
        // Изображение загружено - получаем самую маленькую версию
        if ( ! $avatar = $avatar->getUrl($size) )
        {
            return $nophoto;
        }
        
        return $avatar;
    }
    
    /**
     * Получить список изображений для элемента Carousel в Twitter Bootstrap 
     * 
     * @return array
     * 
     * @todo перенести эту функцию в виджет EThumbCarousel
     */
    public function getBootstrapPhotos($size="medium")
    {
        $tbPhotos = array();
        if ( ! $photos = $this->getGalleryPhotos() )
        {
            return array();
        }
        
        $num = 0;
        foreach ($photos as $photo)
        {
            $element = array();
            $element['id']    = $photo->id;
            $element['num']   = $num;
            $element['image'] = $photo->getUrl($size);
            if ( $photo->name )
            {
                $element['label'] = $photo->name;
            }
            if ( $photo->description )
            {
                $element['caption'] = $photo->description;
            }
            
            $tbPhotos[] = $element;
            $num++;
        }
        
        return $tbPhotos;
    }
}

/* Список всех публичных полей анкеты - он часто нужен
id
userid
firstname
lastname
middlename
birthdate
gender
timecreated
timefilled
timemodified
height
weight
wearsizemin
wearsizemax
shoessize
city
cityid
mobilephone
homephone
addphone
vkprofile
looktype
haircolor
eyecolor
physiquetype
isactor
hasfilms
isemcee
isparodist
istwin
ismodel
titsize
chestsize
waistsize
hipsize
isdancer
hasawards
isstripper
striptype
striplevel
issinger
singlevel
ismusician
issportsman
isextremal
isathlete
hasskills
hastricks
haslanuages
wantsbusinesstrips
hasinshurancecard
inshurancecardnum
hasforeignpassport
passportexpires
addressid
policyagreed
status
encrypted
isphotomodel
ispromomodel
iscoloredhair
newhaircolor
rating
countryid
salary
hastatoo
galleryid*/