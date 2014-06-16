<?php

/**
 * Модель анкеты участника
 * Таблица "{{questionaries}}"
 * 
 * Все данные об участнике хранятся здесь, или в связанных таблицах
 * Модель User используется только для хранения служебных данных (логин, email и так далее)
 * Если нужно добавить новое поле в анкету - то нужно сначала ответить на несколько вопросов:
 * - действительно ли это поле должно быть привязано к пользователю? 
 *   (например дополнительные поля, указываемые при подаче заявки должны быть привязаны к заявке)
 * - нужно ли искать по этому полю? (если да - то лучше всего добавить его сюда)
 * - знаете ли вы про максимальное ограничение MySQL в 64 индекса на таблицу?
 *   (пока не была сформирована четкая политика того что требуется от актеров и какие значения чаще ищутся
 *   мы добавили и убрали из анкеты много полей. Часть из них нужно будет вынести в отдельные связанные
 *   таблицы, в первую очередь все данные для документов и все комментарии, кроме privatecomment
 *   которое нужно для наших пометок, не видно пользователям и скорее всего мы захотим по нему искать)
 * - 
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
 * @property integer $ownerid
 * @property integer $ismassactor
 * @property integer $isamateuractor
 * @property integer $istheatreactor
 * @property integer $istvshowmen - да, имя этого поля это орфографическая ошибка в имени переменной
 *                                  (должно быть istvshowman или hastvshows)
 *                                  К счастью это единственный такой случай во всем приложении
 *                                  Переименовать поле в базе без наличия тестов я не рискую, 
 *                                  поэтому пока что пишем так
 * @property integer $currentcountryid
 * @property integer $visible
 * 
 * Relations: @see http://www.yiiframework.com/wiki/280/1-n-relations-sometimes-require-cdbcriteria-together/
 * @property User $user 
 * @property Address $address
 * @property QRecordingConditions $recordingconditions
 * @property ProjectMember[] $pendingrequests
 * @property Video[] $video
 * @property QCreationHistory[] $creationHistory
 * 
 * Поля для подсчета статистики:
 * @todo уже недостаточно гибкие для новой системы, нужно будет переписать их с применением именованных
 *       групп условий с параметрами (проще говоря прописать и использовать scopes() в связанных моделях)
 * @property int invitesCount - количество непрочитанных приглашений
 * @property int requestsCount - количество поданых заявок
 * @property int pendingRequestsCount
 * @property int upcomingEventsCount
 * 
 * @todo переписать функции проверки "подходит/не подходит на роль" вычислять его в момент отправки приглашений 
 *       чтобы редактирование анкеты после подачи заявки не приводило к неожиданным эффектам, 
 *       если после редактирования пользователь перестает подходить по критериям роли
 * @todo серьезно подумать над тем, нужно ли писать is в начале большинства tinyint полей таблицы
 *       has точно нужно а вот is скорее всего лишнее: судя по таблицам в иностранных модулях
 *       все прогрессивное человечество так не делает, но переименовывать поля к которым обращается
 *       почти вся система довольно накладно, поэтому пока что используем то что есть и если добавляем новые
 *       то тоже с is или has (для единообразия, чтобы не путаться)
 * @todo переименовать поле istvshowmen в istvshowman или hastvshows
 * @todo добавить связь photos
 * @todo удалить все модели и таблицы "instances" для сложных значений: вместо них сделать одну таблицу
 *       q_instances в которой хранятся все связи по qid + objecttype + objectid
 * @todo для всех сложных значений добавить типизированные коллекции: 
 *       @see http://yiiframework.ru/doc/cookbook/ru/model.dao
 */
class Questionary extends CActiveRecord
{
    /**
     * @var string - статус анкеты: черновик
     *               Анкета только что создана и еще не заполнена
     */
    const STATUS_DRAFT       = 'draft';
    /**
     * @var string - статус анкеты: проверена
     *               Анкета проверена и одобрена модератором
     *               Видна в каталоге и поиске
     */
    const STATUS_ACTIVE      = 'active';
    /**
     * @var string - статус анкеты: ожидает проверки
     *               В анкете были изменены какие-то важные данные и она была отправлена на модерацию
     */
    const STATUS_PENDING     = 'pending';
    /**
     * @var string - статус анкеты: отложена.
     *               Анкета была не до конца заполнена оператором и отложена, чтобы заполнить ее позднее
     *               В каталоге и поиске не отображается.
     */
    const STATUS_DELAYED     = 'delayed';
    /**
     * @var string - статус анкеты: отклонена
     *               Анкеты была проверена администратором и отправлена на доработку
     */
    const STATUS_REJECTED    = 'rejected';
    /**
     * @var string - статус анкеты: еще не подтверждена участником.
     *               Используется только если анкеты заводятся без обзвона людей.
     *               (например, кто-то поделился с нами своей базой и мы отсылаем специальные приглашения,
     *               в которых нужно подтверлить либо отказаться от созданной анкеты)
     *               Если анкета находится в этом статусе - это означает, что она уже была введена оператором,
     *               но мы еще не получили от участника согласие на размещение его данных.
     */
    const STATUS_UNCONFIRMED = 'unconfirmed';
    
    /**
     * @var string - размер одежды "меньше 36" (как он хранится в базе данных)
     *               (нужен чтобы нормально работали SQL запросы на поиск "больше/меньше")
     */
    const WEARSIZE_MIN = 1;
    /**
     * @var string - размер одежды "больше 56" (как он хранится в базе данных)
     *               (нужен чтобы нормально работали SQL запросы на поиск "больше/меньше")
     */
    const WEARSIZE_MAX = 99;
    
    /**
     * @var string - размер обуви "меньше 36" (как он хранится в базе данных)
     *               (нужен чтобы нормально работали SQL запросы на поиск "больше/меньше")
     */
    const SHOESSIZE_MIN = 1;
    /**
     * @var string - размер обуви "больше 45" (как он хранится в базе данных)
     *               (нужен чтобы нормально работали SQL запросы на поиск "больше/меньше")
     */
    const SHOESSIZE_MAX = 99;
    
    /**
     * @var string - значение стоящее в select-списках в параметре value в пункте "выбрать"
     *               константа нужна на случай если мы захотим изменить "не выбранное значение"
     *               во всех списках значений анкеты
     */
    const VALUE_NOT_SET = "";
    /**
     * @var string - максимальное количество последних приглашений, отображаемое пользователю
     * @todo убрать константу и добавить настройку анкеты 
     */
    const LAST_INVITES_COUNT = 20;
    
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        parent::init();
        
        Yii::import('application.modules.questionary.extensions.behaviors.*');
        // модели проектов требуются для функций подсчета статистики
        // @todo и это хороший вопрос - нужны ли они здесь?
        //       Нужно решить, следует обращаться 
        //       за статистикой приглашений к самим приглашениям (в модуль Projects), передавая им id участника
        //       а не к от модели участника требовать список или количество приглашений
        //       + приглашения и заявки относятся к участнику и логично иметь в его модели функции запроса таких данных
        //       + чтобы посмотреть приглашения не нужно знать о том где они лежат
        //       - теущее решение требует подключения моделей из другого модуля, это плохо для изолированности
        Yii::import('application.modules.projects.models.*');
        
        // обработчики событий
        $module = Yii::app()->getModule('questionary');
        // один из админов ввел новую анкету 
        $this->attachEventHandler('onNewUserCreatedByAdmin', array($module, 'updateCreationHistory'));
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
     * Получить полный список всех полей, которые могут присутствовать в форме анкеты
     * @return array
     * @deprecated
     */
    public static function getAllFields()
    {
        return array(
            // поля анкеты
            'firstname', 'lastname', 'middlename', 'birthdate', 'gender', 'height', 
            'weight', 'shoessize', 'cityid', 'mobilephone', 'homephone', 
            'addphone', 'vkprofile', 'looktype', 'haircolor', 'eyecolor', 'physiquetype', 
            'isactor', 'hasfilms', 'isemcee', 'isparodist', 'istwin', 'ismodel', 'titsize', 
            'chestsize', 'waistsize', 'hipsize', 'isdancer', 'hasawards', 'isstripper', 'striptype', 
            'striplevel', 'issinger', 'singlevel', 'ismusician', 'issportsman', 'isextremal', 
            'isathlete', 'hasskills', 'hastricks', 'haslanuages',
            'status', 'isphotomodel', 'ispromomodel', 'rating', 
            'fbprofile', 'okprofile', 'isamateuractor', 'istvshowmen', 
            'isstatist', 'ismassactor', 'nativecountryid', 'playagemin', 
            'playagemax', 'hairlength', 'istheatreactor', 'ismediaactor', 
            'privatecomment', 'wearsize', 'currentcountryid',
            // поля модели User
            'email', 'policyagreed',
            // условия съемки
            'salary', 'wantsbusinesstrips', 'hasforeignpassport', 'passportexpires', 'isnightrecording', 
            'istoplessrecording', 'isfreerecording', 'custom', 
            // сложные значения
            'addchars', 'actoruniversities', 'films', 'emceelist', 'parodistlist', 'twinlist', 'modelschools',
            'modeljobs', 'photomodeljobs', 'promomodeljobs', 'dancetypes', 'awards', 'vocaltypes',
            'voicetimbres', 'instruments', 'musicuniversities', 'sporttypes', 'extremaltypes',
            'tricks', 'skills', 'languages', 'tvshows', 'theatres', 'video', 'photo',
        );
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
        // правила проверки простых (скалярных) значений формы
        // проверки по умолчанию
        $defaultScalarRules = $this->getDefaultScalarRules();
        // дополнительные проверки
        $customScalarRules  = $this->getCustomScalarRules();
        
        return CMap::mergeArray($defaultScalarRules, $customScalarRules);
    }
    
    /**
     * Группы условий поиска
     * @see CActiveRecord::scopes()
     */
    public function scopes()
    {
        return array(
            // только подтвержденные (одобренные модератором) анкеты
            'active' => array(
                'condition' => "`status` = '".self::STATUS_ACTIVE."'"
            ),
            // только те анкеты, которые могут подавать заявки
            'admitted' => array(
                'condition' => "`status` IN ('".self::STATUS_ACTIVE."','".self::STATUS_PENDING."','".self::STATUS_REJECTED."')"
            ),
        );
    }

    /**
     * @see CActiveRecord::relations()
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
            // страна проживания
            'currentcountry' => array(self::BELONGS_TO, 'CSGeoCountry', 'currentcountryid'),
            // домашний адрес (все адреса хранятся в отдельной таблице)
            'address' => array(self::HAS_ONE, 'Address', 'objectid',
                'condition' => "`address`.`objecttype` = 'questionary'",
            ),
            // условия участия в съемках
            'recordingconditions' => array(self::HAS_ONE, 'QRecordingConditions', 'questionaryid'),
            
            
            // Значения анкеты, хранящиеся в других таблицах
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
            'modelschools' => array(self::HAS_MANY, 'QModelSchool', 'questionaryid'),
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
            // @todo переписать с использованием именованых условий поиска
            'video' => array(self::HAS_MANY, 'Video', 'objectid',
                'condition' => "`video`.`objecttype` = 'questionary'",
            ),
            
            // Метаданные: (история создания, изменения, данные о доп. полях и т. д.)
            // история создания: может хранить одновременно несколько записей, то есть изначально происхождение
            // данных анкеты может иметь несколько источников: например анкета создана админом (первый источник)
            // для конкретной роли (второй источник)
            'creationHistory' => array(self::HAS_MANY, 'QCreationHistory', 'questionaryid'),
            
            // Связи с проектами и мероприятиями
            // Новые (еще не просмотренные) приглашения на мероприятия
            // @todo переписать с использованием именованых условий поиска
            'invites' => array(self::HAS_MANY, 'EventInvite', 'questionaryid', 
                'condition' => "`invites`.`status` = 'pending' AND `deleted` = 0",
                'limit'     => self::LAST_INVITES_COUNT,
            ),
            // Старые (уже просмотренные) приглашения на мероприятия
            // (все что не в статусе черновик)
            // @todo переписать с использованием именованых условий поиска
            'oldinvites' => array(self::HAS_MANY, 'EventInvite', 'questionaryid', 
                'condition' => "`invites`.`status` != 'draft' `deleted` = 0",
                'limit'     => self::LAST_INVITES_COUNT,
            ),
            // Все заявки на участие в мероприятиях (неподтвержденные + предварительно отобранные)
            'requests' => array(self::HAS_MANY, 'MemberRequest', 'memberid'),
            // Только предварительно подтвержденные заявки на участие
            // @todo переписать с использованием именованых условий поиска
            'pendingrequests' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`memberinstances`.`status`='pending'",
            ),
            // Участие во всех мероприятиях (подтвержденные заявки)
            // @todo переписать с использованием именованых условий поиска
            'memberinstances' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`memberinstances`.`status` IN ('active', 'finished', 'succeed', 'failed')",
            ),
            // Активность в текущих мероприятиях (предстоящие съемки)
            // @todo переписать с использованием именованых условий поиска
            'activememberinstances' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`activememberinstances`.`status`='active'",
            ),
            // История участия во всех прошедших мероприятиях
            // @todo переписать с использованием именованых условий поиска
            'finishedmemberinstances' => array(self::HAS_MANY, 'ProjectMember', 'memberid', 
                'condition' => "`finishedmemberinstances`.`status` IN ('active', 'finished', 'succeed', 'failed')",
            ),
            // @todo Проекты, (сделать связь типа "мост" + DISTINCT)
            
            
            // Статистика
            // Все заявки на участие в мероприятиях
            // @todo переписать с использованием именованых условий поиска
            'requestsCount' => array(self::STAT, 'MemberRequest', 'memberid'),
            // Предварительно одобренные заявки на участие
            // @todo переписать с использованием именованых условий поиска
            'pendingRequestsCount' => array(self::STAT, 'ProjectMember', 'memberid',
                'condition' => "`status`='pending'",
            ),
            // Новые (еще не просмотренные) приглашения на мероприятия
            // @todo переписать с использованием именованых условий поиска
            'invitesCount' => array(self::STAT, 'EventInvite', 'questionaryid',
                'condition' => "`status`='pending' AND `deleted`=0",
            ),
            // Предстоящие съемки (время при выборке не учитываем, они сами завершатся когда нужно)
            // @todo переписать с использованием именованых условий поиска
            'upcomingEventsCount' => array(self::STAT, 'ProjectMember', 'memberid',
                'condition' => "`status`='active'",
            ),
        );
    }

    /**
     * @see parent::behaviors
     */
    public function behaviors()
    {
        // получаем настройки галереи изображений из модуля
        $gallerySettings = Yii::app()->getModule('questionary')->gallerySettings;
        
        Yii::import('ext.galleryManager.*');
        Yii::import('ext.galleryManager.models.*');
        Yii::import('questionary.extensions.behaviors.QManageDefaultValuesBehavior');
        Yii::import('questionary.extensions.behaviors.QManageScalarValueBehavior');
        Yii::import('questionary.extensions.behaviors.QScalarRules');
        
        return array(
            // сохранение и получение скалярных полей анкеты
            'QManageScalarValueBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QManageScalarValueBehavior',
            ),
            // работа со значениями по умолчанию
            'QManageDefaultValuesBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QManageDefaultValuesBehavior',
            ),
            // подключаем behavior для загрузки изображений в анкету актера
            'galleryBehavior' => $gallerySettings,
            // автоматическое заполнение дат создания и изменения
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'timecreated',
                'updateAttribute' => 'timemodified',
            ),
            // проверки для простых полей формы
            'QScalarRules' => array(
                'class' => 'questionary.extensions.behaviors.QScalarRules',
            ),
            // @todo подключать это поведение динамически и только при отображении списка анкет
            'CdGridView' => array(
                'class' => 'questionary.extensions.behaviors.CdGridViewQuestionaryBehavior',
                'ajaxOptions' => array(
                    'url'  => Yii::app()->createUrl('//questionary/questionary/ajaxGetUserInfo'),
                    'data' => array('displayType' => 'searchResults'),
                ),
            ),
        );
    }
    
    /**
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
     * 
     * @todo добавить исключения если связанные записи не создались
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
        if ( $this->isFirstSave() AND Yii::app()->user->checkAccess('Admin') )
        {// если админ ввел анкету - запоминаем это
            $params = array(
                'questionaryId' => $this->id,
                'objectType'    => 'user',
                'objectId'      => Yii::app()->getModule('user')->user()->id,
            );
            $event = new CModelEvent($this, $params);
            $this->onNewUserCreatedByAdmin($event);
        }
        
        parent::afterSave();
    }
    
    /**
     * @todo при рефакторинге переписать удаление через события. Слушать дочерними объектами родительский
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
        $relations = array(
            'memberinstances', 'requests', 'invites', 'tvshows', 'languages', 'skills', 'tricks',
            'extremaltypes', 'sporttypes', 'musicuniversities', 'instruments', 'voicetimbres', 'vocaltypes', 
            'awards', 'dancetypes', 'promomodeljobs', 'photomodeljobs', 'modeljobs', 'modelschools',
            'twinlist', 'parodistlist', 'emceelist', 'films', 'actoruniversities', 'addchars', 'theatres',
            'video',
        );
        
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
     * Эта функция проверяет обязательное наличие хотя бы одной загруженной фотографии
     * @see CModel::beforeValidate()
     */
    public function beforeValidate()
    {
        if ( Yii::app()->user->checkAccess('Admin') )
        {
            return parent::beforeValidate();
        }
        if ( ! $this->isNewRecord AND ! $this->hasPhotos($this->galleryid) )
        {
            $this->addError('galleryid', 'Нужно загрузить хотя бы одну фотографию в разделе "внешность"');
        }
        
        return parent::beforeValidate();
    }
    
    /**
     * Событие "админом введена новая анкета"
     * @param CModelEvent $event
     * @return void
     */
    public function onNewUserCreatedByAdmin($event)
    {
        $this->raiseEvent('onNewUserCreatedByAdmin', $event);
    }
    
    /**
     * Получить все поля для одного раздела при редактировании анкеты
     * @param string $section - название раздела анкеты
     * @return array - полный список полей которые относятся к переданому разделу
     * 
     * @todo при отображении и редактировании разделы отличаются. Нужно учесть этот момент
     * @todo нужно сначала спросить, в каких съемках человек хотел бы участвовать, затем в каком качестве
     *       (актер, модель пародист и т. д.) и только потом всю необходимую для этого информацию
     * @deprecated
     */
    public function getSectionFields($section, $type='edit')
    {
        $fields = array();
        $fields['main'] = array(
            'email', 'firstname', 'lastname', 'middlename', 'birthdate', 
            'playagemin', 'playagemax', 'gender', 'password', 'cityid',
        );
        $fields['contacts'] = array(
            'mobilephone', 'homephone', 'addphone', 'vkprofile', 'fbprofile', 'okprofile',
        );
        $fields['looks'] = array(
            'photo', 'video', 'looktype', 'haircolor', 'hairlength', 'eyecolor', 'physiquetype',
            'chestsize', 'waistsize', 'hipsize',
            'height', 'weight', 'wearsize', 'shoessize', 'hastatoo', 'addchars',
        );
        $fields['skills'] = array(
            'isactor', 'isamateuractor', 'hasfilms', 'istheatreactor', 'isstatist', 'ismassactor',
            'isemcee', 'istvshowmen', 'isparodist', 'istwin', 'ismodel', 'isphotomodel', 'ispromomodel',
            'isdancer', 'isstripper', 'issinger', 'ismusician', 'issportsman', 'isextremal',
            'isathlete', 'hasskills', 'hastricks', 'haslanuages', 'hasawards',
        );
        $fields['conditions'] = array(
            'isnightrecording', 'istoplessrecording', 'isfreerecording', 'wantsbusinesstrips',
            'hasforeignpassport', 'salary', 'custom',
        );
        $fields['settings'] = array(
            
        );
        $fields['admin'] = array(
            'rating', 'status', 'ismediaactor', 'privatecomment', 
        );
        if ( ! isset($fields[$section]) )
        {
            throw new CException('Неизвестный раздел анкеты: '.$section);
        }
        
        return $fields[$section];
    }
    
    /**
     * Определить, загружена ли хотя бы одна фотография
     * 
     * @param int $galleryId
     * @return boolean
     * 
     * @todo эта функция должна быть вынесена в Gallery либо в behavior 
     */
    protected function hasPhotos($galleryId)
    {
        if ( ! $gallery = Gallery::model()->findByPk($galleryId) )
        {
            return false;
        }
        if ( $gallery->galleryPhotos )
        {// фотографии есть
            return true;
        }
        return false;
    }

    /**
     * @return array customized attribute labels (name=>label)
     * @todo навести порядок в подписях
     */
    public function attributeLabels()
    {
        $defaultLabels = array(
            'id' => 'ID',
            'userid' => QuestionaryModule::t('userid_label'),
            'firstname' => QuestionaryModule::t('firstname_label'),
            'lastname' => QuestionaryModule::t('lastname_label'),
            'middlename' => QuestionaryModule::t('middlename_label'),
            'birthdate' => QuestionaryModule::t('birthdate_label'),
            'age' => QuestionaryModule::t('age_label'),
            'formattedBirthDate' => QuestionaryModule::t('birthdate_label'),
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
            'galleryid' => QuestionaryModule::t('photos_label'),

            // пояснения, не являющиеся полями
            'level' => QuestionaryModule::t('level'),
            'type' => QuestionaryModule::t('type'),
            'passport' => QuestionaryModule::t('passport_data'),
            'wearsize' => QuestionaryModule::t('wearsize_label'),
            'address' => QuestionaryModule::t('address'),
            
            // поля, хранящиеся в других таблицах
            'addchar' => QuestionaryModule::t('addchar_label'),
            'university' => QuestionaryModule::t('universities_label'),
            'sporttype' => QuestionaryModule::t('sporttype_label'),
            'extremaltype' => QuestionaryModule::t('extremaltype_label'),
            'skill' => QuestionaryModule::t('skill_label'),
            'vocaltype' => QuestionaryModule::t('vocaltype_label'),
            'photos' => QuestionaryModule::t('photos_label'),
            
        );
        
        return $defaultLabels;
    }
    
    /**
     * 
     * @return void
     */
    public function dynamicAttributeLabels()
    {
        $labels = array();
        $fields = QUserField::model()->findAll();
        
        foreach ( $fields as $field )
        {
            $labels[$field->name] = QuestionaryModule::t($field->name.'_label');
        }
        return $labels;
    }
    
    /**
     * 
     * @param string $name
     * @return string
     */
    public function getDynamicAttributeLabel($name)
    {
        $labels = $this->dynamicAttributeLabels();
        if ( ! isset($labels[$name]) )
        {
            return '[['.$name.']]';
        }
        return $labels[$name];
    }

    /**
     * Получить список достижений и умений участника: спортсмен, актер, атлет, и т. д.
     * @return array - список всех достижений участника, склоняемый в зависимости от пола
     *                 формат: "поле_анкеты" => "отображаемое значение"
     */
    public function getBages()
    {
        $bages = array();
        // актер (актриса)
        if ( $this->isactor )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['isactor'] = QuestionaryModule::t('actor');
            }else
            {
                $bages['isactor'] = QuestionaryModule::t('actress');
            }
        }
        // непрофессиональный актер
        if ( $this->isamateuractor AND ! $this->isactor )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['isamateuractor'] = QuestionaryModule::t('amateuractor(male)');
            }else
            {
                $bages['isamateuractor'] = QuestionaryModule::t('amateuractor(female)');
            }
        }
        // ведущий (ведущая)
        if ( $this->isemcee )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['isemcee'] = QuestionaryModule::t('emcee(male)');
            }else
            {
                $bages['isemcee'] = QuestionaryModule::t('emcee(female)');
            }
        }
        // телеведущий
        if ( $this->istvshowmen )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['istvshowmen'] = QuestionaryModule::t('tvshowmen(male)');
            }else
            {
                $bages['istvshowmen'] = QuestionaryModule::t('tvshowmen(female)');
            }
        }
        // пародист
        if ( $this->isparodist )
        {
            $bages['isparodist'] = QuestionaryModule::t('parodist');
        }
        // двойник
        if ( $this->istwin )
        {
            $bages['istwin'] = QuestionaryModule::t('twin');
        }
        // модель (скрываем чтобы мужики не стеснялись)
        if ( $this->ismodel AND $this->gender === 'female' )
        {
            $bages['ismodel'] = QuestionaryModule::t('model');
        }
        // фотомодель (скрываем чтобы мужики не стеснялись)
        if ( $this->isphotomodel AND $this->gender === 'female' )
        {
            $bages['isphotomodel'] = QuestionaryModule::t('photomodel');
        }
        // промо-модель (скрываем чтобы мужики не стеснялись)
        if ( $this->ispromomodel AND $this->gender === 'female' )
        {
            $bages['ispromomodel'] = QuestionaryModule::t('promomodel');
        }
        // танцор
        if ( $this->isdancer )
        {
            $bages['isdancer'] = QuestionaryModule::t('dancer');
        }
        // стриптиз
        if ( $this->isstripper )
        {
            $bages['isstripper'] = QuestionaryModule::t('stripper');
        }
        // вокал
        if ( $this->issinger )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['issinger'] = QuestionaryModule::t('singer(male)');
            }else
            {
                $bages['issinger'] = QuestionaryModule::t('singer(female)');
            }
        }
        // музыкант
        if ( $this->ismusician )
        {
            $bages['ismusician'] = QuestionaryModule::t('musician');
        }
        // каскадер
        if ( $this->hastricks )
        {
            $bages['hastricks'] = QuestionaryModule::t('hastricks');
        }
        // спортсмен(ка)
        if ( $this->issportsman )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['issportsman'] = QuestionaryModule::t('sportsman(male)');
            }else
            {
                $bages['issportsman'] = QuestionaryModule::t('sportsman(female)');
            }
        }
        // экстремал
        if ( $this->isextremal )
        {
            $bages['isextremal'] = QuestionaryModule::t('extremal');
        }
        // атлет
        if ( $this->isathlete )
        {
            $bages['isathlete'] = QuestionaryModule::t('athlete');
        }
        // актер театра
        if ( $this->istheatreactor )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['istheatreactor'] = QuestionaryModule::t('theatreactor(male)');
            }else
            {
                $bages['istheatreactor'] = QuestionaryModule::t('theatreactor(female)');
            }
        }
        // медийный актер
        if ( $this->ismediaactor )
        {
            if ( ! $this->gender OR $this->gender === 'male' )
            {
                $bages['ismediaactor'] = QuestionaryModule::t('mediaactor(male)');
            }else
            {
                $bages['ismediaactor'] = QuestionaryModule::t('mediaactor(female)');
            }
        }
        // @todo сейчас мы список сортируем в алфавитном порядке, но возможно следует перечислять
        //       в порядке важности? Если да - то важности для кого?
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
     * 
     * @deprecated не используется после полной переработки формы, удалить при рефакторинге
     *             Использовалась в самом начале работы, когда еще не было возможности отслеживать
     *             заполнение и изменение анкеты
     *             Сейчас при редактировании анкеты все поля проверяются и сохраняются через AJAX
     *             поэтому больше нет никакой необходимости прописывать сложные правила проверки для модели
     *             или менять в ней что-то после сохранения
     *             До этого мы убирали статус при отсутствии нужных данных, сейчас наоборот: 
     *             добавляем нужный статус если введено хотя бы одно необходимое значение
     */
    protected function autoModeration()
    {
        $dependences = array(
            'isactor'        => 'actoruniversities',
            'hasfilms'       => 'films',
            'isemcee'        => 'emceelist',
            'istvshowmen'    => 'tvshows',
            'isparodist'     => 'parodistlist',
            'istwin'         => 'twinlist',
            'isdancer'       => 'dancetypes',
            'haslanuages'    => 'languages',
            'hasawards'      => 'awards',
            'istheatreactor' => 'theatres',
        );
        
        foreach ( $dependences as $field => $relation )
        {// Перезагружаем связаные записи чтобы не наткнуться на кеш
            // поэтому используем getRelated()
            $data = $this->getRelated($relation, true);
            if ( isset($this->$field) AND $this->$field AND ! $data )
            {// галочка выставлена, а необходимых данных нет - сбросим ее обратно
                $this->$field = 0;
            }
        }
        if ( $this->isamateuractor  )
        {// если поставлена галочка "непрофессиональный актер"
            // то должен быть указан или театр или фильмография
            if ( ! $this->istheatreactor AND ! $this->hasfilms )
            {
                $this->isamateuractor = 0;
            }
            // а также сбрасывается галочка "профессиональный актер"
            $this->isactor = 0;
        }
        
        if ( $this->isactor )
        {// если участник - профессиональный актер - уберем галочку "непрофессиональный актер"
            // потому что это взаимоисключающие пункты
            $this->isamateuractor = 0;
        }
    }
    
    /**
     * Определить, должна ли анкета попасть к модератору после сохранения пользователем
     * Анкета отправляется к модератору только если были включены любые поля, отвечающие за умения
     * (например спортсмен, музыкант, вокал, и т. п.)
     * Исключение - актер массовых сцен и статист - эти поля могут быть указаны кем угодно и не требуют проверки
     * модератором
     * @deprecated не используется после полной переработки формы, удалить при рефакторинге
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
     * @deprecated не будет использоваться после ввода новой системы оповещений, поле admincomment
     *             должно быт убрано из этой модели
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
     * @todo присылать разные сообщения для участника, заказчика и члена команды
     * @deprecated не используется после перехода на систему оповещений по событиям, 
     *             удалить после введения в эту модель SimpleWorkflow
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
        
        if ( $this->ownerid AND $this->ownerid == 823 )
        {// нужно особое письмо с активацией
            if ( ( $oldStatus == 'draft' OR $oldStatus == 'delayed' ) AND
                 ( $newStatus == 'unconfirmed' OR $newStatus == 'active' ) )
            {
                $this->sendSSFillingMail();
            }
        }else
        {// обычная активация
            if ( ( $oldStatus == 'draft' OR $oldStatus == 'delayed' ) AND
                 ( $newStatus == 'unconfirmed' OR $newStatus == 'active' ) )
            {// если анкета только что перешла из статуса "черновик" в активный статус -
                // значит пользователя только что зарегистрировали и ввели все данные
                $filled = true;
            }
            if ( ! Yii::app()->user->checkAccess('Admin') OR ! $filled )
            {// Эта функция используется только если анкету заполняет админ и только при первом сохранении анкеты
                return;
            }
            if ( ! QCreationHistory::model()->forQuestionary($this->id)->forType('vacancy') )
            {// @todo если анкета была создана админом для специальной роли
                // то отсылать письмо специально для этой роли (текст должен лежать внутри роли)
                $this->sendDefaultFillingMail($user);
            }
        }
    }
    
    /**
     * Отправить письмо участнику с предложением зарегистрироваться
     * (для анкет из базы Светланы Строиловой)
     * @return null
     */
    protected function sendSSFillingMail()
    {
        $mailComposer = Yii::app()->getModule('mailComposer');
         
        $email   = $this->user->email;
        $subject = 'Приглашение от проекта easyCast';
        $message = $mailComposer->getMessage('SSInvite', array('questionary' => $this));
        
        UserModule::sendMail($this->user->email, $subject, $message, true);
    }
    
    /**
     * @todo - переписать с использованием mailComposer
     * @param unknown $user
     * @return null
     */
    protected function sendDefaultFillingMail($user)
    {
        $mailComposer = Yii::app()->getModule('mailComposer');
         
        $email   = $this->user->email;
        $subject = 'Приглашение от проекта easyCast';
        $message = $mailComposer->getMessage('ECRegistration', array('questionary' => $this));
        
        UserModule::sendMail($this->user->email, $subject, $message, true);
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
        
        if ( $old->status === 'delayed' AND $old->status != $new->status )
        {// анкета перестает быть отложеной
            return true;
        }
        if ( $old->status === 'draft' AND $new->status != 'delayed' )
        {// сохраняется черновик анкеты, и анкета не откладывается администратором чтобы заполнить позже
            return true;
        }
        if ( ! $this->timemodified )
        {// анкета еще ни разу не сохранялась
            return true;
        }
        return false;
    }
    
    /**
     * Подтверждена ли анкета модератором?
     * @return bool
     */
    public function isVerified()
    {
        if ( self::STATUS_ACTIVE == $this->status )
        {
            return true;
        }
        return false;
    }
    
    /**
     * Разрешено ли участнику подавать заявку на роль? 
     * @return boolean
     */
    public function isAdmitted()
    {
        if ( in_array($this->status, array(self::STATUS_ACTIVE, self::STATUS_PENDING, self::STATUS_REJECTED)) )
        {
            return true;
        }
        return false;
    }
    
    /**
     * Получить администрарора, который ввел анкету
     * @return User|null - объект из таблицы users или null если участник зарегистрировался сам
     */
    public function getQuestionaryAuthor()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('questionaryid', $this->id);
        $criteria->order = "id ASC";
        $criteria->limit = 1;
        
        if ( ! $creationRecord = current(QCreationHistory::model()->findAll($criteria)) )
        {// отсутствует информация о том кто создал эту анкету
            return null;
        }
        if ( ! $user = User::model()->findByPk($creationRecord->userid) )
        {// создавший анкету администратор не найден в нашей базе
            // @todo записать ошибку в лог
            return null;
        }
        return $user;
    }
    
    /////////////////////////////////////////
    // Именованные группы условий (scopes) //
    /////////////////////////////////////////
    
    /**
     * Получить все анкеты, созданные пользователем или объектом системы (именованная группа условий)
     * @param string $objectType - тип объекта в таблице q_creation_history (user/vacancy/...)
     * @param int $objectId
     * @return Questionary
     */
    public function createdBy($objectType, $objectId)
    {
        $criteria = new CDbCriteria();
        $criteria->with = 'creationHistory';
        // together Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        $criteria->compare('`creationHistory`.`objecttype`', $objectType);
        $criteria->compare('`creationHistory`.`objectid`', $objectId);
         
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
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
     * 
     * @deprecated после перехода на новые виджеты редактирования списков значений не используется
     *             Удалить при рефакторинге
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
    // (значений, которые принадлежат анкете, но хранятся в таблице "q_activities")
    
    /**
     * Сохранить тембры голоса, которыми владеет пользователь
     * @param array $values
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setvoicetimbre($values)
    {
        $this->saveSimpleActivity('voicetimbre', $values);
    }
    
    /**
     * Дополнительные характеристики
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setaddchar($values)
    {
        $this->saveSimpleActivity('addchar', $values);
    }
    
    /**
     * Образы пародиста
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setparodist($values)
    {
        $this->saveSimpleActivity('parodist', $values);
    }
    
    /**
     * Образы двойника
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function settwin($values)
    {
        $this->saveSimpleActivity('twin', $values);
    }
    
    /**
     * Типы вокала
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setvocaltype($values)
    {
        $this->saveSimpleActivity('vocaltype', $values);
    }
    
    /**
     * Виды спорта
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setsporttype($values)
    {
        $this->saveSimpleActivity('sporttype', $values);
    }
    
    /**
     * Экстремальные виды спорта
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setextremaltype($values)
    {
        $this->saveSimpleActivity('extremaltype', $values);
    }
    
    /**
     * Выполнение трюков
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function settrick($values)
    {
        $this->saveSimpleActivity('trick', $values);
    }
    
    /**
     * Умения и навыки
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setskill($values)
    {
        $this->saveSimpleActivity('skill', $values);
    }
    
    /**
     * Иностранные языки
     * @param array $values - данные из сложного поля формы
     * @deprecated не используется (требовалось для старой формы анкеты), удалить при рефакторинге
     *             Сейчас редактирование любых списков привязанных к анкете значений происходит
     *             только виджетами, основанными на QGridEditBase
     */
    public function setlanguage($values)
    {
        $this->saveSimpleActivity('language', $values);
    }
    
    // преобразования скалярных полей формы
    
    /** 
     * Получить список полей, которые нуждаются в преобразовании после прихода из формы
     * 
     * @return multitype:string
     * @deprecated
     * @todo удалить при рефакторинге
     */
    public function convertedFields()
    {
        return array(/*'birthdate',*/ 'passportexpires', 'passportdate');
    }
    
    /** 
     * Получить массив атрибутов модели, пригодных для записи в базу
     * @todo посмотреть, можно ли сделдать это нормальным способом через фильтры
     * 
     * @param array $attributes - пришедшие из $_POST данные
     * @deprecated
     * @todo удалить при рефакторинге
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
    
    /** 
     * Преобразовать значение из формы в значение хранимое в базе
     * 
     * @param array $result - массив с итоговым результатом полей
     * @param string $field - поле формы из которого пришли данные
     * @param string|int|array $value - значение поля
     * 
     * @return mixed 
     * 
     * @deprecated
     * @todo удалить при рефакторинге
     */
    protected function convertFieldValue($result, $field, $value)
    {
        switch ($field)
        {
            //case 'birthdate':       return $this->convertDate($result, $field, $value); break;
            case 'passportexpires': return $this->convertDate($result, $field, $value); break;
            case 'passportdate':    return $this->convertDate($result, $field, $value); break;
            
            default: return $result;
        }
    }
    
    /** 
     * Преобразовать значения полей даты из массива в unixtime
     * 
     * @param array $result
     * @param array $field
     * @param array $value
     * 
     * @deprecated
     * @todo удалить при рефакторинге
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
     * @deprecated пока еще используется старым кодом, все новые функции должны использовать
     *             QActivityType::model()->activityVariants($name); вместо этого метода
     * @todo найти и заменить все вызовы этого метода на вызовы QActivityType
     */
    public function getComplexFieldVariants($field)
    {
        return $this->activityVariants($field);
    }
    
    /**
     * Получить значение одного стандартного варианта сложного поля
     * @param string $type
     * @param string $value
     * 
     * @deprecated пока еще используется старым кодом, оставлено для совместимости
     *             новые функции должны использовать методы классов QActivityType и QActivity
     *             для работы со списками таких значений
     * @todo найти и заменить все вызовы этого метода на вызовы QActivityType
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
     * 
     * @deprecated пока еще используется старым кодом, оставлено для совместимости
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
     * 
     * @deprecated пока еще используется старым кодом, оставлено для совместимости
     */
    protected function activityVariants($name)
    {
        return QActivityType::model()->activityVariants($name);
    }
    
    /**
     * Определить, отображать по умолчанию указанное поле формы 
     * (или часть формы в которой содержится нескольких полей)
     * 
     * @param string $section - поле формы или название набора полей
     *                          
     * @return bool
     */
    public function isDisplayedSection($section)
    {
        $result = true;
        
        // Большинство полей формы изначально скрыто чтобы не перегружать интерфейс
        // Как правило видимость поля или виджета зависит от элементов в которых есть только 
        // значения "да/нет" 
        // Составим карту того, какие поля (или блоки полей) от каких галочек зависят
        
        // Ключами массива являются поля или секции полей, которые нужно показать или скрыть
        // Они совпадают со списком связей (relations) в модели Questionary
        // Значениями - поля модели questionary, которые содержат варианты "да" или "нет" 
        $dependences = array(
            // Театральные ВУЗы (скрыто, если не выбрано "профессиональный актер")
            'actoruniversities' => 'isactor',
            // Фильмография (скрыто, если не указано что пользователь снимался в фильмах)
            'films'             => 'hasfilms',
            // Ведущий (скрыто, если не выставлена галочка)
            'emceelist'         => 'isemcee',
            // Телеведущий (скрыто без галочки)
            'tvshows'           => 'istvshowmen',
            // Пародист (скрыто, если не выставлена галочка)
            'parodist'          => 'isparodist',
            // Двойник (скрыто, если не выставлена галочка)
            'twin'              => 'istwin',
            // Модельные школы (скрыто, если нет галочки "модель")
            'modelschools'      => 'ismodel',
            // Показы (скрыто, если нет галочки "модель")
            'modeljobs'         => 'ismodel',
            // Работа фотомоделью (скрыто, если нет галочки)
            'photomodeljobs'    => 'isphotomodel',
            // Работа промо-моделью (скрыто, если нет галочки)
            'promomodeljobs'    => 'ispromomodel',
            // Стили танца (скрыто, если нет галочки "танцор")
            'dancetypes'        => 'isdancer',
            // Тип и уровень стриптиза (Только если выбран "стриптиз")
            'stripdata'         => 'isstripper',
            // Типы вокала (скрыто, если не выбран "вокал")
            'vocaltypes'        => 'issinger',
            // Тембр голоса (скрыто, если не выбран "вокал")
            'voicetimbres'      => 'issinger',
            // Уровень вокала (скрыто, если не выбран "вокал")
            'singlevel'         => 'issinger',
            // Музыкальные инструменты (Только если выбран "музыкант")
            'instruments'       => 'ismusician',
            // Виды спорта (Скрыто, если не выбран "спортсмен")
            'sporttypes'        => 'issportsman',
            // Экстремальные виды спорта (скрыто, если не выбрано "экстремал")
            'extremaltypes'     => 'isextremal',
            // Список дополнительных умений и навыков (скрыто, если нет галочки)
            'skills'            => 'hasskills',
            // Список трюков (скрыто, если нет галочки)
            'tricks'            => 'hastricks',
            // Список иностранных языков (скрыто, если нет галочки)
            'languages'         => 'haslanuages',
            // Номер страхового свидетельства (скрыто, если нет свидетельства)
            'inshurancecardnum' => 'hasinshurancecard',
            // Срок действия загранпаспорта (скрыто, если его нет)
            'passportexpires'   => 'hasforeignpassport',
            // Список наград (скрыто, если нет галочки)
            'awards'            => 'hasawards',
            // Опыт работы в театре
            'actortheatres'     => 'istheatreactor',
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
     * @return string - url картинки или заглушку, если у пользователя пока нет ни одной фотографии
     * @todo этот метод следует вынести из анкеты, но пока еще неизвестно куда
     *       возможно в отдельный behavior, так как в процессе разработки
     *       такой метод пришлось добавить во все модели, которые требуют небольшой preview-картинки
     *       при отображении (например класс Project)
     *       При переносе этого метода нужно заглянуть в модель Project и сделать там то же самое
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