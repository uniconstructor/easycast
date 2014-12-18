<?php

/**
 * Модель для работы с мероприятиями и группами мероприятий проекта 
 * Таблица "{{project_events}}"
 *
 * Колонки таблицы '{{project_events}}':
 * @property integer $id
 * @property string $projectid
 * @property string $name
 * @property string $description
 * @property string $timestart
 * @property string $timeend
 * @property string $timecreated
 * @property string $timemodified
 * @property string $addressid
 * @property string $status
 * @property string $type
 * @property string $parentid
 * @property string $memberinfo
 * @property string $showtimestart
 * @property string $eta
 * @property string $meetingplace
 * @property string $nodates
 * @property string $virtual - "виртуальное" мероприятие любого типа: то есть такое событие которое 
 *                             физически нигде не проводится и присутствует только на сайте
 *                             (например онлайн-кастинг)
 * 
 * 
 * Связи с другими таблицами:
 * @property Project $project - проект к которому привязано мероприятие
 * @property Address $address - адрес, по которому проходит мероприятие 
 * @property array $vacancies - Вакансии мероприятия 
 * @property array $activevacancies - опубликованные вакансии мероприятия
 * @property array $invites - Приглашения на мероприятие
 * @property array $videos - Видео c мероприятия
 * @property array $events - (для групп) мероприятия, входящие в группу
 * @property ProjectEvent $group - группа, к которой принадлежит мероприятие (если есть)
 * @property ProjectMember[] $members - подтвержденные участники мероприятия (НЕ ГОТОВО)
 * 
 * @todo прописать связь members - все участники мероприятия
 * @todo прописать условие с параметром vacancies - список ролей с параметром "статус"
 * @todo прописать создание адреса в afterSave
 * @todo убрать поле addressid, прописать связь через condition
 * @todo языковые строки
 * @todo документировать все константы типов мероприятия
 * @todo решить, что делать при удалении группы: удалять все дочерние мероприятия или просто убирать их из группы
 * @todo вместо строк везде использовать константы статусов 
 * @todo перенести defaultTimeStart и defaultTimeEnd, а также функцию получения событий для календаря в модуль календаря
 * @todo добавить статус "suspended"
 * @todo добавить статусы "внесена информация" и "открыта запись"
 */
class ProjectEvent extends CActiveRecord
{
    /**
     * @var int - максимальное количество фотографий в галерее мероприятия
     */
    const MAX_GALLERY_PHOTOS = 10;
    
    // типы мероприятий
    /**
     * @var string - тип мероприятия: отсутствует (обычное мероприятие)
     */
    const TYPE_EVENT      = 'event';
    /**
     * @var string - тип мероприятия: группа мероприятий. Не является съемочным днем, в отличии от остальных типов.
     *               Мероприятия такого типа служат контейнерами, в которые собираются другие мероприятия.
     *               Группы не имеют дат начала и окончания.
     *               Если для группы создается вакансия - то это означает, что участник подавая заявку 
     *               обязуется присутствовать на всех мероприятиях группы.
     *               Мероприятия группы активируются только вместе с ней (но завершаться могут отдельно).
     * @deprecated использовать списки
     */
    const TYPE_GROUP      = 'group';
    /**
     * @var string - тип мероприятия: кастинг. Вакансии для такого меропритятия могут создаваться без оплаты.
     *               Итог кастинга для участника определяется конечным статусом заявки:
     *               успешно завершена (succeed) - участник прошел кастинг
     *               неуспешно завершена (failed) - участник не прошел кастинг или вообще не пришел на него
     */
    const TYPE_CASTING    = 'casting';
    /**
     * @var string - тип мероприятия: фотосессия (например перед кастингом)
     */
    const TYPE_PHOTO      = 'photo';
    /**
     * @var string - тип мероприятия: репетиция.
     */
    const TYPE_REPETITION = 'repetition';
    /**
     * @var string - тип мероприятия: генеральная репетиция. Присутствие строго обязательно.
     */
    const TYPE_PRESHOW    = 'preshow';
    /**
     * @var string - тип мероприятия: съемки. Главный день группы событий или проекта.
     *               Присутствие строго обязательно.
     */
    const TYPE_SHOW       = 'show';
    
    // статусы мероприятия
    /**
     * @var string - статус: черновик
     *               Мероприятие только что создано, в него пока еще вносится информация и добавляются роли
     */
    const STATUS_DRAFT    = 'draft';
    /**
     * @var string - статус: опубликовано
     *               Мероприятие опубликовано на сайте, на него приглашены участники, подаются заявки, 
     *               или оно проходит в данный момент (ведутся съемки, проходит кастинг и т. д.)
     */
    const STATUS_ACTIVE   = 'active';
    /**
     * @var string - статус: завершено
     *               Мероприятие завершено, съемки окончены.
     */
    const STATUS_FINISHED = 'finished';
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectEvent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @see CActiveRecord::init()
	 */
	public function init()
	{
	    parent::init();
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{project_events}}';
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 * 
	 * @todo устанавливать автоматически поля "nodates", "showtimestart", "timestart", "timeend" 
	 *       если тип мероприятия - группа
	 */
	public function beforeSave()
	{
	    // @todo перенести это преобразование даты в фильтры модели или заменить этот виджет на более удобный
	    // @todo использовать Yii::app()->getDateFormatter()
	    if ( $timestart = CDateTimeParser::parse($this->timestart, 'dd.MM.yyyy HH:mm') )
	    {
	        $this->timestart = $timestart;
	    }
	    if ( $timeend = CDateTimeParser::parse($this->timeend, 'dd.MM.yyyy HH:mm') )
	    {
	        $this->timeend = $timeend;
	    }
	    if ( $this->type == self::TYPE_GROUP )
	    {// группы мероприятий не имеют конкретной даты
	        $this->nodates   = 1;
	    }
	    if ( $this->nodates )
	    {// события без конкретной даты не могут иметь продолжительности
	        $this->timestart = 0;
	        $this->timeend   = 0;
	    }
	    // выполняем служебные действия ActiveRecord
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterDelete()
	 */
	protected function afterDelete()
	{
	    foreach ( $this->vacancies as $vacancy )
	    {// при удалении мероприятия удаляем все вакансии
	        $vacancy->delete();
	    }
	    foreach ( $this->invites as $invite )
	    {// и все приглашения на него
	        $invite->delete();
	    }
	    if ( $this->type == 'group' )
	    {// если удаляется группа - то удаляем и все дочерние мероприятия
	        foreach ( $this->events as $event )
	        {
	            $event->delete();
	        }
	    }
	    if ( $this->address )
	    {// А также адрес
	        $this->address->delete();
	    }
	    
	    // выполняем служебные действия ActiveRecord
	    parent::afterDelete();
	}
	
	/**
	 * @return array relational rules.
	 *
	 * @todo переписать с использованием именованных групп условий
	 */
	public function relations()
	{
	    $relations = array(
	        // проект к которому привязано мероприятие
	        'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
	        // адрес, по которому проходит мероприятие
	        'address' => array(self::HAS_ONE, 'Address', 'objectid',
	            'condition' => "`address`.`objecttype`='event'",
	        ),
	        // группа мероприятия (если это мероприятие входит в группу)
	        'group' => array(self::BELONGS_TO, 'ProjectEvent', 'parentid'),
	        // Вакансии (роли) мероприятия
	        'vacancies' => array(self::HAS_MANY, 'EventVacancy', 'eventid'),
	        // активные вакансии мероприятия
	        // @deprecated
	        'activevacancies' => array(self::HAS_MANY, 'EventVacancy', 'eventid',
	            'scopes' => array(
	                'withStatus' => array(EventVacancy::STATUS_ACTIVE),
	            ),
	        ),
	        // Приглашения на мероприятие
	        'invites' => array(self::HAS_MANY, 'EventInvite', 'eventid'),
	        // Видео c мероприятия
	        'videos' => array(self::HAS_MANY, 'Video', 'objectid',
	            'condition' => "`videos`.`objecttype`='projectevent'",
	        ),
	        // дочерние мероприятия группы (если это является группой)
	        'events' => array(self::HAS_MANY, 'ProjectEvent', 'parentid',
	            'order' => '`events`.`timestart` ASC',
	        ),
	        // @todo подтвержденные участники мероприятия
	        //'members' => array(self::HAS_MANY, 'ProjectMember', ......),
	    );
	    // подключаем связи для настроек
	    if ( ! $this->asa('ConfigurableRecordBehavior') )
	    {
	        $this->attachBehavior('ConfigurableRecordBehavior', array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
	            'defaultOwnerClass' => get_class($this),
	        ));
	    }
	    $configRelations = $this->asa('ConfigurableRecordBehavior')->getDefaultConfigRelations();
	    return CMap::mergeArray($relations, $configRelations);
	}
	
	/**
	 * @see parent::behaviors
	 */
	public function behaviors()
	{
	    Yii::import('ext.galleryManager.*');
	    Yii::import('ext.galleryManager.models.*');
	    // Настройки фотогалереи для мероприятия
	    $photoGallerySettings = array(
	        'class'       => 'GalleryBehavior',
	        'idAttribute' => 'photogalleryid',
	        'limit'       => self::MAX_GALLERY_PHOTOS,
	        // картинка проекта масштабируется в трех размерах
	        'versions' => array(
	            'small'  => array(
	                'resize' => array(100, 100),
	            ),
	            'medium' => array(
	                'resize' => array(530, 330),
	            ),
	            'full'   => array(
	                'resize' => array(800, 1000),
	            ),
	        ),
	        // галерея будет без имени
	        'name'        => false,
	        'description' => true,
	    );
	    return array(
	        // автоматическое заполнение дат создания и изменения
            'EcTimestampBehavior' => array(
                'class' => 'application.behaviors.EcTimestampBehavior',
            ),
	        // это поведение позволяет изменять набор связей модели в зависимости от того какие данные в ней находятся
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationTargetBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
	            'customRelations' => array(),
	        ),
	        // настройки для модели и методы для поиска по этим настройкам
	        'ConfigurableRecordBehavior' => array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
	        ),
	        // фотогалерея
	        'photoGalleryBehavior' => $photoGallerySettings,
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 * 
	 * @todo прописать проверки более четко
	 * @todo сделать timestart, timeend обязательными если не выставлена галочка nodates
	 */
	public function rules()
	{
		return array(
			array('name, description', 'required'),
			array('nodates, eta, showtimestart, parentid, projectid, timestart, timeend, timecreated, 
			    timemodified, addressid, virtual', 'length', 'max' => 20),
			array('meetingplace, memberinfo, description', 'length', 'max' => 4095),
			array('status', 'length', 'max' => 9),
			array('type', 'length', 'max' => 20),
			array('name', 'length', 'max' => 255),
		    // проверка даты начала через фильтр
		    array('timestart', 'ext.YiiConditionalValidator',
		        'if' => array(
		            array(
		                'timestart', 'date',
		                'allowEmpty' => false,
		                'format'     => Yii::app()->params['yiiDateTimeFormat'],
		            ),
		        ),
		        'then' => array(
		            array(
		                'timestart', 'filter',
		                'filter' => array('EcDateTimeParser', 'parseDateTime'),
		            ),
		        ),
		    ),
		    // проверка даты окончания через фильтр
	        array('timeend', 'ext.YiiConditionalValidator',
	            'if' => array(
	                array(
	                    'timeend', 'date',
	                    'allowEmpty' => false,
	                    'format'     => Yii::app()->params['yiiDateTimeFormat'],
	                ),
	            ),
	            'then' => array(
	                array(
	                    'timeend', 'filter',
	                    'filter' => array('EcDateTimeParser', 'parseDateTime'),
	                ),
	            ),
	        ),
			// The following rule is used by search().
			//array('id, projectid, name, description, timestart, timeend, timecreated, timemodified, addressid, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'projectid' => ProjectsModule::t('project'),
			'name' => ProjectsModule::t('name'),
			'description' => ProjectsModule::t('description'),
			'timestart' => ProjectsModule::t('timestart'),
			'timeend' => ProjectsModule::t('timeend'),
			'timecreated' => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
			'addressid' => ProjectsModule::t('event_addressid'),
			'status' => ProjectsModule::t('status'),
			'type' => 'Тип мероприятия',
			'parentid' => 'Группа',
			'memberinfo' => 'Дополнительная информация для участников',
			'showtimestart' => 'Отображать ли время начала съемок?',
			'eta' => 'Время сбора',
			'meetingplace' => 'Место встречи',
			'nodates' => 'Создать мероприятие без конкретной даты',
		);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    // условия поиска по датам создания и изменения
	    $timestampScopes = $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	    // собственные условия поиска модели
        $modelScopes = array(
	        // первые начавшиеся
	        'firstStarted' => array(
	            'order' => $this->getTableAlias(true).'.`timestart` ASC',
	        ),
	        // последние начавшиеся
	        'lastStarted' => array(
	            'order' => $this->getTableAlias(true).'.`timestart` DESC',
	        ),
	        // первые закончившиеся
	        'firstEnded' => array(
	            'order' => $this->getTableAlias(true).'.`timeend` ASC',
	        ),
	        // последние закончившиеся
	        'lastEnded' => array(
	            'order' => $this->getTableAlias(true).'.`timeend` DESC',
	        ),
	        // последние созданные записи
	        'lastCreated' => array(
	            'order' => $this->getTableAlias(true).'.`timecreated` DESC',
	        ),
	        // последние измененные записи
	        'lastModified' => array(
	            'order' => $this->getTableAlias(true).'.`timemodified` DESC',
	        ),
	        // только с конкретной датой
	        'withDate' => array(
	            'condition' => $this->getTableAlias(true).'.`nodates` = 0',
	        ),
	        // только без конкретной даты
	        'withoutDate' => array(
	            'condition' => $this->getTableAlias(true).'.`nodates` = 1',
	        ),
	        // начинаются до текущего момента (прошедшие)
	        'startsBeforeNow' => array(
	            'condition' => $this->getTableAlias(true).'.`timestart` < '.time(),
	        ),
	        // начинаются после текущего момента (актуальные)
	        'startsAfterNow' => array(
	            'condition' => $this->getTableAlias(true).'.`timestart` > '.time(),
	        ),
	        // только события-группы
	        'groupsOnly' => array(
	            'condition' => $this->getTableAlias(true).".`type` = '".self::TYPE_GROUP."'",
	        ),
	        // все события кроме групп
	        'exceptGroups' => array(
	            'condition' => $this->getTableAlias(true).".`type` <> '".self::TYPE_GROUP."'",
	        ),
	        // только события которые находятся внутри какой-либо группы
	        // @todo переименовать в inGroup
	        'hasGroup' => array(
	            'condition' => $this->getTableAlias(true).'.`parentid` > 0',
	        ),
	        // только отдельные события не входящие ни в одну группу
	        /** @deprecated устаревшее название, удалить при рефакторинге */
	        'hasNoGroup' => array(
	            'scopes' => array('withoutGroup'),
	        ),
	        // только отдельные события не входящие ни в одну группу
	        'withoutGroup' => array(
	            'condition' => $this->getTableAlias(true).'.`parentid` = 0',
	        ),
	        // только события у которых есть активные роли
	        'hasActiveVacancies' => array(
	            'scopes' => array(
	               'withVacancies' => array(self::STATUS_ACTIVE),
    	        ),
	        ),
	    );
        return CMap::mergeArray($timestampScopes, $modelScopes);
    }
    
    /**
     * Именованная группа условий поиска - выбрать только "не пустые" события - 
     * то есть события хотя бы с одной ролью внутри
     * 
     * @param  array|string $statuses - массив статусов ролей или строка если статус один
     *                                 (чтобы можно было найти только события с активными ролями)
     * @return ProjectEvent
     */
    public function withVacancies($statuses=array(), $operator='AND')
    {
        $criteria       = new CDbCriteria();
        $criteria->with = array(
            'vacancies' => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'withStatus' => array($statuses),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->getDbCriteria()->mergeWith($criteria);
        
        return $this;
    }
    
    /**
     * Именованная группа условий поиска - выбрать записи по статусам
     * 
     * @param  array|string $statuses - массив статусов или строка если статус один
     * @return ProjectEvent
     * 
     * @todo удалить после подключения simpleWorkflow
     */
    public function withStatus($statuses=array(), $operator='AND')
    {
        if ( ! $statuses )
        {// условие не используется
            return $this;
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->getTableAlias(true).'.`status`', $statuses);
        
        $this->getDbCriteria()->mergeWith($criteria);
    
        return $this;
    }
    
    /**
     * Именованная группа условий поиска - выбрать мероприятия с указанным типом
     * 
     * @param  array|string $type - имп мероприятия
     * @return ProjectEvent
     */
    public function withType($type=array(), $operator='AND')
    {
        if ( ! $type )
        {// условие не используется
            return $this;
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->getTableAlias(true).'.`type`', $type);
        
        $this->getDbCriteria()->mergeWith($criteria);
         
        return $this;
    }
    
    /**
     * Именованная группа условий поиска - выбрать мероприятия по типу проекта
     * 
     * @param array $types - типы проектов к которым принадлежат извлекаемые мероприятия
     * @return ProjectEvent
     */
    public function withProjectType($projectTypes=array(), $operator='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->with = array(
            'project' => array(
                'select'   => false,
                'scopes'   => array(
                    'withType' => $projectTypes,
                ),
            ),
        );
        $criteria->together = true;
        
        $this->getDbCriteria()->mergeWith($criteria);
         
        return $this;
    }
    
    /**
     * Именованная группа условий поиска - все мероприятия проекта (поиск только по id)
     * Если передан массив id - то будут выбраны все мероприятия, которые присутствуют 
     * в любом из перечисленных проектов
     *
     * @param  array|int $projectId - id проекта (один или несколько)
     * @return ProjectEvent
     */
    public function withProjectId($projectId, $operator='AND')
    {
        if ( ! is_array($projectId) )
        {
            $projectId = intval($projectId);
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->getTableAlias(true).'.`projectid`', $projectId);
    
        $this->getDbCriteria()->mergeWith($criteria);
         
        return $this;
    }
    
    /**
     * Именованная группа условий поиска - все мероприятия проекта (alias)
     * (этот метод используется когда нужно найти события по модели проекта)
     * 
     * @param  int|array|Project $project - id проекта для которого ищутся мероприятия, массив id,
     *                                      или одна модель проекта
     * @return ProjectEvent
     */
    public function forProject($project, $operator='AND')
    {
        if ( is_object($project) AND (get_class($project) === 'Project') )
        {
            $projectId = $project->id;
        }else
        {
            $projectId = $project;
        }
        return $this->withProjectId($projectId, $operator);
    }
    
    /**
     * Именованная группа условий поиска - получить все мероприятия проекта,
     * в которых участвовал (подавал заявки или снимался) определенный пользователь
     * 
     * @param int $questionaryId - id анкеты участника
     * @param array $statuses - статусы поданых заявок от участника заявок (чтобы можно было найти только
     *                          те мероприятия на которые заявки этого участника были приняты или отклонены)
     *                          если статус не указан - он не добавляется в условие поиска
     * @return ProjectEvent
     */
    public function containingQuestionary($questionaryId, $statuses=array())
    {
        $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'vacancies' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'containingQuestionary' => array($questionaryId, $statuses),
	            ),
	        ),
	    );
	    $criteria->together = true;
	    
        $this->getDbCriteria()->mergeWith($criteria);
        
        return $this;
    }
    
    /**
     * Именованая группа условий: все события начинающиеся до определенного времени
     * 
     * @param int $time - unix timestamp
     * @return ProjectEvent
     */
    public function startsBefore($time=null)
    {
        if ( null === $time )
        {
            $time = time();
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->getTableAlias(true).'.`timestart`', '<='.$time);
        
        $this->getDbCriteria()->mergeWith($criteria);
         
        return $this;
    }
    
    /**
     * Именованая группа условий: все события начинающиеся после определенного времени
     * 
     * @param int $time - unix timestamp
     * @return ProjectEvent
     */
    public function startsAfter($time=null)
    {
        if ( null === $time )
        {
            $time = time();
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->getTableAlias(true).'.`timestart`', '>='.$time);
        
        $this->getDbCriteria()->mergeWith($criteria);
         
        return $this;
    }

	/**
	 * Получить список событий для календаря
	 * 
	 * @param string $timestart - начало периода
	 * @param string $timeend - конец периода
	 * @param string $projectid - id проекта, (если нужно просмотреть только события проекта)
	 * @param string $userid - id анкеты участника в таблице questionary по которому получается информация
	 * @param string|array $projecttypes - тип(или типы) проекта
	 * @param bool $eventslist - получить только активные событий?
	 * @return string - JSON-массив событий для подстановки в календарь
	 * 
	 * @todo добавить проверку прав
	 * @todo сделать более умное извлечение событий по пользователю
	 * @todo переписать поиск по критериям при помощи $params
	 */
	public function getCalendarEvents($timeStart=null, $timeEnd=null, $projectId=null, 
	                                        $userId=null, $projectType=null, $onlyActive=false)
	{
	    $events   = array();
	    $criteria = new CDbCriteria();
	    // не выводим в календаре мероприятия без дат
	    $criteria->compare('nodates', 0);
	    // не выводим группы
	    $criteria->compare('type', "<>group");
	    $criteria->order = '`timestart` ASC';
	    
	    // устанавливаем стандартный интервал времени для запроса событий (если он не указан пользователем)
	    $timeStart = $this->getDefaultTimeStart($timeStart);
	    $timeEnd   = $this->getDefaultTimeEnd($timeEnd);
	    
	    if ( $onlyActive )
	    {// нужны только активные мероприятия
	        $criteria->addInCondition('status', array('active'));
	    }else
	    {// нужны все мероприятия
	        $criteria->addInCondition('status', array('active', 'finished'));
	    }
	    
	    // составляем запрос к базе
	    // Временной отрезок (учитываем только время начала события чтобы все влезло в календарь)
	    //$criteria->compare('timestart', '>='.$timeStart);
	    //$criteria->compare('timestart', '<='.$timeEnd);
	    
	    if ( $projectType )
	    {// нужны только мероприятия определенного типа
	        $criteria->with = 'project';
	        if ( is_array($projectType) )
	        {
	            $criteria->addInCondition('project.type', $projectType);
	        }else
	        {
	            $criteria->compare('project.type', $projectType);
	        }
	    }
	    
	    if ( $projectId )
	    {// выбрать только мероприятия определенного проекта
	        $criteria->compare('projectid', $projectId);
	    }
	    
	    if ( $userId )
	    {// выбрать только мероприятия, в которых участвует пользователь
	        if ( ! ( Yii::app()->getModule('user')->user()->id == $userId OR Yii::app()->user->checkAccess('Admin') ) )
	        {// разрешаем смотреть календарь пользователя только самому пользователю или админу
	            return CJSON::encode(array());
	        }
	        // оставляем только те события, в которых участвует пользователь
	        $recordset = $this->model()->findAll($criteria);
	        $records   = array();
	        foreach ( $recordset as $record )
	        {
	            if ( $record->hasMember($userId) )
	            {
	                $records[] = $record;
	            }
	        }
	    }else
        {
            $records = $this->model()->findAll($criteria);
        }
        // Конвертируем события в формат календаря
        $events = $this->convertEventsToCalendar($records);
        
        return CJSON::encode($events);
	}
	
	/**
	 * Преобразовать события в формат, пригодный для использования в календаре
	 * 
	 * @param array $events
	 * @return string
	 * 
	 * @todo убрать ссылку когда будет сделано всплывающее окно в календаре
	 */
	protected function convertEventsToCalendar($events)
	{
	    $result = array();
	    foreach ( $events as $event )
	    {
	        $instance = array();
	        $instance['id']     = $event->id;
	        $instance['title']  = $event->name;
	        $instance['allDay'] = false;
	        $instance['start']  = $event->timestart;
	        $instance['end']    = $event->timeend;
	        $instance['url']    =  Yii::app()->createUrl('//projects/projects/view', array('eventid' => $event->id));
	        //$instance['className'] = $event->;
	        $instance['editable'] = false;
	        
	        $result[] = $instance;
	    }
	    return $result;
	}
	
	/**
	 * Определить, является ли переданный участник участником этого события
	 * 
	 * @param int $questionaryId - id анкеты участника в таблице questionary
	 * @return bool
	 */
	public function hasMember($questionaryId)
	{
	    if ( ! $this->vacancies )
	    {// в мероприятии нет ни одной роли - значит нет и участников
	        return false;
	    }
	    foreach ( $this->vacancies as $vacancy )
	    {
	        if ( $vacancy->hasMember($questionaryId) )
	        {
	            return true;
	        }
	    }
	    return false;
	}
	
    /**
	 * Разослать приглашения всем подходящим участникам в базе
	 * 
	 * @return bool
	 */
	public function sendInvites()
	{
	    if ( ! $vacancies = $this->vacancies )
	    {
	        return false;
	    }
	    foreach ( $vacancies as $vacancy )
	    {
	        $vacancy->sendInvites();
	    }
	    return true;
	}
	
	/**
	 * Удалить все приглашения на мероприятие
	 * 
	 * @return int
	 */
	public function deleteInvites()
	{
	     return EventInvite::model()->deleteAll('eventid=:eventid', array(':eventid' => $this->id));
	}
	
	/**
	 * Получить список статусов, в которые может перейти объект
	 * 
	 * @return array
	 */
	public function getAllowedStatuses()
	{
	    switch ( $this->status )
	    {
	        case 'draft':
	            if ( $this->project->status != 'finished' )
	            {
	                return array('active');
	            }
            break;
	        case 'active':
	            return array('finished');
            break;
	    }
	    return array();
	}
	
	/**
	 * Получить статус объекта для отображения пользователю
	 * 
	 * @param string $status
	 */
	public function getStatustext($status=null)
	{
	    if ( ! $status )
	    {
	        $status = $this->status;
	    }
	    return ProjectsModule::t('event_status_'.$status);
	}
	
	/**
	 * Перевести объект из одного статуса в другой, выполнив все необходимые действия
	 * 
	 * @param  string $newStatus
	 * @return bool
	 * 
	 * @todo отдельные статусы разнести по разным функциям
	 * @todo проверять результат каждой операции
	 */
	public function setStatus($newStatus)
	{
	    if ( ! in_array($newStatus, $this->getAllowedStatuses()) )
	    {
	        return false;
	    }
	    
	    /*if ( $newStatus == 'active' AND $this->group AND $this->group->status == self::STATUS_ACTIVE )
	    {// если запускается новое мероприятие в уже активированной группе -
	        // то высылаем дополнительные оповещения по вакансиям группы
	        // @todo придумать как оповещать вакансии группы о новых мероприятиях в группе
	    	foreach ( $this->vacancies as $groupVacancy )
	    	{
	    	    
	    	}
	    }*/
	    
	    $this->status = $newStatus;
	    $this->save();
	    
	    if ( $newStatus == 'active' )
	    {// событие публикуется на сайте
	        if ( $this->type == self::TYPE_GROUP )
	        {// запускается группа - активируем все дочерние события
	            foreach ( $this->events as $event )
	            {
	                $event->setStatus('active');
	            }
	        }
	        foreach ( $this->vacancies as $vacancy )
	        {// активируем все вакансии события
	            if ( $vacancy->status == 'draft' )
	            {
	                $vacancy->setStatus('active');
	            }
	        }
	    }
	     
	    if ( $newStatus == 'finished' )
	    {// событие завершается
	        if ( $this->type == self::TYPE_GROUP )
	        {// завершается группа - завершим все дочерние события
    	        foreach ( $this->events as $event )
    	        {
    	            if ( $event->status == 'active' )
    	            {// активные мероприятия завершаются
    	                $event->setStatus('finished');
    	            }elseif ( $event->status == 'draft' )
    	            {// не начатые - удаляются
    	                $event->delete();
    	            }
    	        }
	        }
	        foreach ( $this->vacancies as $vacancy )
	        {
	            if ( $vacancy->status == 'active' )
	            {// закрываем все вакансии события
	                $vacancy->setStatus('finished');
	            }
	            if ( $vacancy->status == 'draft' )
	            {// если событие завершено - удаляем все не начатые вакансии
	                $vacancy->delete();
	            }
	        }
	        foreach ( $this->invites as $invite )
	        {// все неотвеченные приглашения сгорают с завершением мероприятия
	            if ( $invite->status == EventInvite::STATUS_PENDING  )
	            {/* @var $invite EventInvite */
	                $invite->deleted = 1;
	                $invite->setStatus(EventInvite::STATUS_EXPIRED);
	            }
	        }
	    }
	    return true;
	}
	
	/**
	 * Город по умолчанию для всех создаваемых мероприятий
	 * 
	 * @return int
	 * 
	 * @todo брать из настроек или из шаблона мероприятия
	 */
	public function getDefaultCityId()
	{
	    return 4400;
	}
	
	/**
	 * Получить метку времени, начиная с которой запрашивать события для календаря
	 * 
	 * @param  string $neededTimeStart
	 * @return int
	 */
	public function getDefaultTimeStart($neededTimeStart=null)
	{
	    $defaultTimeStart = time();
	    if ( ! $neededTimeStart )
	    {
	        $timeStart = $defaultTimeStart;
	    }else
	    {
	        $timeStart = $neededTimeStart;
	    }
	    $timeStart = intval($timeStart);
	    
	    return $timeStart;
	}
	
	/**
	 * Получить метку времени, после которой события из календаря запрашивать уже не нужно
	 * 
	 * @param  string $neededTimeEnd - запрошенное пользователем время окончания мероприятия
	 * @return int
	 * 
	 * @todo сделать интервал запроса настройкой
	 */
	public function getDefaultTimeEnd($timeStart, $neededTimeEnd=null)
	{
	    $defaultTimeEnd = $timeStart + 3 * 31 * 24 * 3600;
	    if ( ! $neededTimeEnd )
	    {// пользователь не указал окончание временного периода - устанавливаем по умолчанию
	        $timeEnd = $defaultTimeEnd;
	    }else
	    {
	        $timeEnd = $neededTimeEnd;
	    }
	    
	    $timeEnd = intval($timeEnd);
	    // Защита сервера от перегрузки: не даем запрашивать события больше чем за 3 месяца
	    if ( $timeEnd - $timeStart > 3 * 31 * 24 * 3600 )
	    {// запрашиваются мероприятия за период больше разрешенного - непорядок
	        $timeEnd   = $defaultTimeEnd;
	    }
	    return $timeEnd;
	}
	
	/**
	 * Определить, закончилось ли мероприятие
	 * 
	 * @return boolean
	 */
	public function getExpired()
	{
	    return $this->isExpired();
	}
	
	/**
	 * Определить, закончилось ли мероприятие
	 * 
	 * @return boolean
	 */
	public function isExpired()
	{
	    if ( $this->status === ProjectEvent::STATUS_FINISHED )
	    {// событие завершено
            return true;
	    }
	    // вычисляем как долго мероприятие стоит открытым
	    // @todo вынести максимальный период существования мероприятия в настройку
	    $alivePeriod = time() - $this->timecreated;
	    if ( $this->nodates )
	    {// мероприятия без даты считаются просроченными
	        if ( $alivePeriod > 30 * 24 * 3600 )
	        {// ну только если не висят больше месяца - это беспредел
	            return true;
	        }
	        return false;
	    }
	    if ( $this->timeend < time() )
	    {// мероприятияе уже прошло но еще не завершено администратором
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Получить дату и время начала мероприятия в удобном читаемом виде
	 * 
	 * @return string
	 */
	public function getFormattedTimeStart()
	{
	    if ( $this->type == self::TYPE_GROUP )
	    {
	        return '';
	    }
	    if ( $this->nodates )
	    {// дата мероприятия пока не известна
	        return '[Дата уточняется]';
	    }
	    if ( date('Y', $this->timestart) == 1970 )
	    {// дата не задана - ничего не выводим
	        return '';
	    }
	    return Yii::app()->getDateFormatter()->format('d MMMM HH:mm', $this->timestart);
	}
	
	/**
	 * Получить дату и время окончания мероприятия в удобном читаемом виде
	 *
	 * @return string
	 */
	public function getFormattedTimeEnd()
	{
	    if ( $this->type == self::TYPE_GROUP )
	    {
	        return '';
	    }
	    if ( $this->nodates )
	    {// дата мероприятия пока не известна
	        return '';
	    }
	    if ( date('Y', $this->timeend) == 1970 )
	    {// дата не задана - ничего не выводим
	        return '';
	    }
	    return Yii::app()->getDateFormatter()->format('d MMMM HH:mm', $this->timeend);
	}
	
	/**
	 * Получить дату и время начала и окончания мероприятия в удобном читаемом виде
	 * 
	 * @return null
	 */
	public function getFormattedTimePeriod($showDuration=false)
	{
	    if ( $this->nodates OR ($this->timestart == $this->timeend) )
	    {// мероприятие без конкретной даты - так и скажем
	        return $this->getFormattedTimeStart();
	    }
	    return $this->getFormattedTimeStart().' - '.Yii::app()->getDateFormatter()->format('HH:mm', $this->timeend);
	}
	
	/**
	 * Получить дату события в стандартном формате
	 * 
	 * @return string
	 */
    public function getFormattedDate()
    {
        return Yii::app()->getDateFormatter()->format('d MMMM', $this->timestart);
    }
    
    /**
     * Получить ссылку на просмотр этого события
     * 
     * @param  array $params - дополнительные параметры для ссылки (если нужно)
     * @return string
     */
    public function getUrl($params=array())
    {
        return Yii::app()->createUrl('//projects/projects/view', array('eventid' => $this->id));
    }
	
	/**
	 * Получить список всех возможных типов мероприятия (для select-списков)
	 * 
	 * @return array
	 */
	public function getTypes($ignoreRestrictions=false)
	{
	    $types = array();
	    $types[self::TYPE_EVENT] = 'Нет (обычное мероприяте)';
	    /*if ( $this->isNewRecord OR ! $this->events OR $ignoreRestrictions )
	    {// группу можно указать только при создании новой записи
	        // и изменить этот тип, только если в ней еще нет ни одного мероприятия
	        $types[self::TYPE_GROUP] = 'Группа мероприятий';
	    }*/
	    $types[self::TYPE_CASTING]    = 'Кастинг';
	    $types[self::TYPE_PHOTO]      = 'Фотосессия';
	    $types[self::TYPE_REPETITION] = 'Репетиция';
	    $types[self::TYPE_PRESHOW]    = 'Генеральная репетиция';
	    $types[self::TYPE_SHOW]       = 'Съемка';
	    
	    return $types;
	}
	
	/**
	 * Получить перевод типа события для пользователя
	 * 
	 * @param  string $type[optional]
	 * @return string
	 */
	public function getTypeLabel($type=null)
	{
	    if ( ! $type )
	    {
	        $type = $this->type;
	    }
	    $types = $this->getTypes(true);
	    if ( isset($types[$type]) )
	    {
	        return $types[$type];
	    }
	}
	
	/**
	 * Получить список тех групп, в которые может быть добавлено это мероприятие
	 * (для формирования выпадающего списка групп)
	 * 
	 * @return array
	 * 
	 * @todo запретить добавлять активные события в запланированные группы
	 */
	public function getOpenGroups($projectId=null)
	{
	    if ( ! $projectId )
	    {
	        $projectId = $this->projectid;
	    }
	    $result = array('0' => 'Без группы');
	    
	    $criteria = new CDbCriteria();
	    $criteria->compare('type', 'group');
	    $criteria->compare('status', array('draft', 'active'));
	    $criteria->compare('projectid', $projectId);
	    $criteria->select = '`id`, `name`';
	    $groups = self::model()->findAll($criteria);
	    
	    foreach ( $groups as $group )
	    {
	        $result[$group->id] = $group->name;
	    }
	    if ( $this->id AND isset($result[$this->id]) )
	    {// группу нельзя запихнуть в саму себя :)
	        unset($result[$this->id]);
	    }
	    return $result;
	}
	
	/**
	 * Получить спискок вакансий, доступных указанному участнику
	 * 
	 * @param int $questionaryId - id анкеты участника
	 * @param bool $showGroup - добавлять ли к вакансиям события вакансии ее группы?
	 * @return array - массив вакансий, доступных участнику или пустой массив,
	 *                 если ни одной подходящей вакансии нет
	 * 
	 * @todo добавить к этому списку вакансии группы
	 * @todo решить, каким образом лучше всего проверять доступность ролей группы для участника
	 *       при помощи списков: возможно следует разрешить иметь не более 1 набора условий
	 *       для всей группы мероприятий 
	 */
	public function getAllowedVacancies($questionaryId, $showGroup=false)
	{
	    $vacancies = array();
	    // проверяем каждую вакансию мероприятия, и определяем, подходит ли для нее участник
	    foreach ( $this->vacancies as $vacancy )
	    {/* @var $vacancy EventVacancy */
	        if ( $vacancy->isAvailableForUser($questionaryId) )
	        {
	            $vacancies[$vacancy->id] = $vacancy;
	        }
	    }
	    return $vacancies;
	}
	
	/**
	 * Подсчитать количество доступных ролей для выбраного пользователя в текущем мероприятии
	 * 
	 * @param  int $questionaryId - id анкеты для которой считается количество доступных ролей
	 * @return int
	 */
	public function countVacanciesFor($questionaryId)
	{
	    return count($this->getAllowedVacancies($questionaryId));
	}
	
	
	/**
	 * Определить, есть ли хоть одна доступная роль для выбранного пользователя в текущем событии
	 * 
	 * @param  int $questionaryId - id анкеты для которой определяются доступные роли
	 * @return boolean
	 * 
	 * @todo оптимизировать алгоритм: не проверять все активные роли, а останавливаться как только нашли первую
	 */
	public function hasVacanciesFor($questionaryId)
	{
	    return (bool)$this->countVacanciesFor($questionaryId);
	}
}