<?php

/**
 * This is the model class for table "{{project_events}}".
 *
 * The followings are the available columns in table '{{project_events}}':
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
 * @property string $salary
 * @property string $meetingplace
 * @property string $nodates
 * 
 * 
 * Связи с другими таблицами:
 * @property Project $project - проект к которому привязано мероприятие
 * @property Address $address - адрес, по которому проходит мероприятие 
 * @property array $vacancies - Вакансии мероприятия 
 * @property array $activevacancies - опубликованные вакансии мероприятия
 * @property array $invites - Приглашения на мероприятие
 * @property array $videos - Видео c мероприятия
 * @property array $events 
 * @property ProjectEvent $group 
 * 
 * @todo прописать создание адреса в afterSave
 * @todo убрать поле addressid, прописать связь через condition
 * @todo языковые строки
 * @todo документировать все константы типов мероприятия
 * @todo решить, что делать при удалении группы: удалять все дочерние мероприятия или просто убирать их из группы
 * @todo прописать все статусы константами 
 * @todo перенести defaultTimeStart и defaultTimeEnd, а также функцию получения событий для календаря в модуль календаря
 */
class ProjectEvent extends CActiveRecord
{
    // типы мероприятия
    /**
     * @var int - максимальное количество фотогрфвий в галерее мероприятия
     */
    const MAX_GALLERY_PHOTOS = 10;
    
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
     * @var string - 
     */
    const STATUS_DRAFT    = 'draft';
    /**
     * @var string -
     */
    const STATUS_ACTIVE   = 'active';
    /**
     * @var string -
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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{project_events}}';
	}
	
	/**
	 * (non-PHPdoc)
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
	    {// группы мероприятий не имеют продолжительности
	        $this->timestart = 0;
	        $this->timeend   = 0;
	        $this->nodates   = 1;
	    }
	    
	    // выполняем служебные действия ActiveRecord
	    return parent::beforeSave();
	}
	
	/**
	 * (non-PHPdoc)
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
	 * @see parent::behaviors
	 */
	public function behaviors()
	{
	    // Настройки фотогалереи для мероприятия
	    $photoGallerySettings = array(
	        'class' => 'GalleryBehavior',
	        'idAttribute' => 'photogalleryid',
	        'limit' => self::MAX_GALLERY_PHOTOS,
	        // картинка проекта масштабируется в трех размерах
	        'versions' => array(
	            'small' => array(
	                'resize' => array(100, 100),
	            ),
	            'medium' => array(
	                'resize' => array(530, 330),
	            ),
	            'full' => array(
	                'resize' => array(800, 1000),
	            ),
	        ),
	        // галерея будет без имени
	        'name'        => false,
	        'description' => true,
	    );
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
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
			array('nodates, eta, showtimestart, parentid, projectid, timestart, timeend, timecreated, timemodified, addressid', 'length', 'max'=>20),
			array('meetingplace, memberinfo, description', 'length', 'max'=>4095),
			array('status', 'length', 'max'=>9),
			array('type', 'length', 'max'=>20),
			array('salary', 'length', 'max'=>32),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('id, projectid, name, description, timestart, timeend, timecreated, timemodified, addressid, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 * 
	 * @todo добавить openGroups - те группы, в которые может быть добавлено мероприятие
	 * @todo придумать как в списки вакансий добавить вакансии группы
	 */
	public function relations()
	{
		return array(
		    // проект к которому привязано мероприятие
		    'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
		    // адрес, по которому проходит мероприятие
		    'address' => array(self::HAS_ONE, 'Address', 'addressid',
		        'condition' => "`address`.`objecttype`='event'"),
		    // группа мероприятия (если это мероприятие входит в группу)
		    'group' => array(self::BELONGS_TO, 'ProjectEvent', 'parentid'),
		    // Вакансии мероприятия
		    'vacancies' => array(self::HAS_MANY, 'EventVacancy', 'eventid', 'order' => "`vacancies`.`name` ASC"),
		    // активные вакансии мероприятия
		    'activevacancies' => array(self::HAS_MANY, 'EventVacancy', 'eventid',
		        'condition' => "`activevacancies`.`status`='active'", 'order' => "`activevacancies`.`name` ASC"),
		    // Приглашения на мероприятие
		    'invites' => array(self::HAS_MANY, 'EventInvite', 'eventid'),
		    // Видео c мероприятия
		    'videos' => array(self::HAS_MANY, 'Video', 'objectid',
		        'condition' => "`videos`.`objecttype`='projectevent'"),
		    // дочерние мероприятия группы (если это является группой)
		    'events' => array(self::HAS_MANY, 'ProjectEvent', 'parentid',
		        'order' => '`events`.`timestart` ASC'),
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
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'addressid' => ProjectsModule::t('event_addressid'),
			'status' => ProjectsModule::t('status'),
			'type' => 'Тип мероприятия',
			'parentid' => 'Группа',
			'memberinfo' => 'Дополнительная информация для участников',
			'showtimestart' => 'Отображать ли время начала съемок?',
			'eta' => 'Время сбора',
			'salary' => 'Размер оплаты',
			'meetingplace' => 'Место встречи',
			'nodates' => 'Создать мероприятие без конкретной даты',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 * 
	 * @todo удалить, не используется
	 */
	/**public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('projectid',$this->projectid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('addressid',$this->addressid,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}*/
	
	/**
	 * Получить список событий для календаря
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
	 * @param array $events
	 * @return string
	 * 
	 * @todo убрать ссылку когда будет сделано всплывающее окно в календаре
	 * @todo убрать хак со сдвигом времени в календаре, когда будет понятно как надежно установить
	 *       временную зону на сервере
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
	        // FIXME временно подгоняем даты событий под московское время пока не настроим сервер нормально
	        //$instance['start']  = $event->timestart - 4 * 3600;
	        $instance['start']  = $event->timestart;
	        //$instance['end']    = $event->timeend - 4 * 3600;
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
	 * @param int $userId - id участника в таблице questionary
	 * @return bool
	 */
	public function hasMember($userId)
	{
	    if ( ! $this->vacancies )
	    {// в проекте нет ни одной вакансии - значит нет и участников
	        return false;
	    }
	    
	    foreach ( $this->vacancies as $vacancy )
	    {
	        if ( $vacancy->hasMember($userId) )
	        {
	            return true;
	        }
	    }
	    
	    return false;
	}
	
    /**
	 * Разослать приглашения всем подходящим участникам в базе
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
	 */
	public function deleteInvites()
	{
	     return EventInvite::model()->deleteAll('eventid=:eventid', array(':eventid' => $this->id));
	}
	
	/**
	 * Получить список статусов, в которые может перейти объект
	 * @todo добавить статус "suspended"
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
	 * @param string $newStatus
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
	            if ( $invite->status == EventInvite::STATUS_PENDING )
	            {
	                $invite->setStatus(EventInvite::STATUS_EXPIRED);
	            }
	        }
	    }
	     
	    return true;
	}
	
	/**
	 * Город по умолчанию для всех создаваемых мероприятий
	 * 
	 * @return number
	 * 
	 * @todo брать из настроек
	 */
	public function getDefaultCityId()
	{
	    return 4400;
	}
	
	/**
	 * Получить метку времени, начиная с которой запрашивать события для календаря
	 * @param string $neededTimeStart
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
	 * @param string $neededTimeEnd - запрошенное пользователем время окончания мероприятия
	 * @return int
	 * 
	 * @todo сделать интервал запроса настройкой
	 */
	public function getDefaultTimeEnd($timeStart, $neededTimeEnd=null)
	{
	    $defaultTimeEnd = $timeStart + 3*31*24*3600;
	    if ( ! $neededTimeEnd )
	    {// пользователь не указал окончание временного периода - устанавливаем по умолчанию
	        $timeEnd = $defaultTimeEnd;
	    }else
	    {
	        $timeEnd = $neededTimeEnd;
	    }
	    
	    $timeEnd = intval($timeEnd);
	    // Защита сервера от перегрузки: не даем запрашивать события больше чем за 3 месяца
	    if ( $timeEnd - $timeStart > 3*31*24*3600 )
	    {// запрашиваются мероприятия за период больше разрешенного - непорядок
	        $timeEnd   = $defaultTimeEnd;
	    }
	    
	    return $timeEnd;
	}
	
	/**
	 * Определить, закончилось ли мероприятие
	 * @return boolean
	 */
	public function getExpired()
	{
	    return $this->isExpired();
	}
	
	/**
	 * Определить, закончилось ли мероприятие
	 * @return boolean
	 */
	public function isExpired()
	{
	    if ( $this->timeend < time() OR $this->status == 'finished' )
	    {
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
	    if ( $this->nodates )
	    {// мероприятие без конкретной даты - так и скажем
	        return $this->getFormattedTimeStart();
	    }
	    
	    return $this->getFormattedTimeStart().' - '.Yii::app()->getDateFormatter()->format('HH:mm', $this->timeend);
	}
	
	/**
	 * Получить список всех возможных типов мероприятия (для select-списков)
	 * @return array
	 */
	public function getTypes($ignoreRestrictions=false)
	{
	    $types = array();
	    $types[self::TYPE_EVENT] = 'Нет (обычное мероприяте)';
	    if ( $this->isNewRecord OR ! $this->events OR $ignoreRestrictions )
	    {// группу можно указать только при создании новой записи
	        // и изменить этот тип, только если в ней еще нет ни одного мероприятия
	        $types[self::TYPE_GROUP] = 'Группа мероприятий';
	    }
	    $types[self::TYPE_CASTING]    = 'Кастинг';
	    $types[self::TYPE_PHOTO]      = 'Фотосессия';
	    $types[self::TYPE_REPETITION] = 'Репетиция';
	    $types[self::TYPE_PRESHOW]    = 'Генеральная репетиция';
	    $types[self::TYPE_SHOW]       = 'Съемки';
	    
	    return $types;
	}
	
	/**
	 * Получить перевод типа события для пользователя
	 * @param string $type[optional]
	 * @return string
	 */
	public function getTypeLabel($type=null)
	{
	    if ( ! $type )
	    {
	        $type = $this->type;
	    }
	    $types = $this->getTypes(true);
	    return $types[$type];
	}
	
	/**
	 * Получить список тех групп, в которые может быть добавлено это мероприятие
	 * (для формирования выпадающего списка групп)
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
	 */
	public function getAllowedVacancies($questionaryId, $showGroup=false)
	{
	    $vacancies = array();
	    
	    if ( ! $this->activevacancies )
	    {
	        return array();
	    }
	    
	    // @todo 
	    /*if ( $this->group )
	    {// если это мероприятие входит в состав группы, то проверим и вакансии группы
    	    // @todo убрать эту проверку, когда все вакансии будут прописаны через relations
    	    $activeVacancies = $this->group->getAllowedVacancies($questionaryId);
	    }*/
	    
	    
	    foreach ( $this->activevacancies as $vacancy )
	    {// проверяем каждую вакансию мероприятия, и определяем, подходит ли для нее участник 
	        if ( $vacancy->isAvailableForUser($questionaryId) )
	        {
	            $vacancies[$vacancy->id] = $vacancy;
	        }
	    }
	    
	    return $vacancies;
	}
}