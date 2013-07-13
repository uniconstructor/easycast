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
 */
class ProjectEvent extends CActiveRecord
{
    /**
     * @var int - максимальное количество фотогрфвий в галерее мероприятия
     */
    const MAX_GALLERY_PHOTOS = 10;
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
	 */
	public function beforeSave()
	{
	    if ( $timestart = CDateTimeParser::parse($this->timestart,'dd.MM.yyyy HH:mm') )
	    {
	        $this->timestart = $timestart;
	    }
	    if ( $timeend = CDateTimeParser::parse($this->timeend,'dd.MM.yyyy HH:mm') )
	    {
	        $this->timeend = $timeend;
	    }
	    
	    return parent::beforeSave();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    // При удалении мероприятия удаляем все вакансии и приглашения на него
	    foreach ( $this->vacancies as $vacancy )
	    {
	        $vacancy->delete();
	    }
	    
	    foreach ( $this->invites as $invite )
	    {
	        $invite->delete();
	    }
	    
	    // А также адрес
	    $this->address->delete();
	}
	
	/**
	 * @see parent::behaviors
	 */
	public function behaviors()
	{
	    // Настройки фотогалереи проекта
	    $photoGallerySettings = array(
	        'class' => 'GalleryBehaviorS3',
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
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, timestart, timeend, description', 'required'),
			array('projectid, timestart, timeend, timecreated, timemodified, addressid', 'length', 'max'=>20),
			array('name, description', 'length', 'max'=>4095),
			array('status', 'length', 'max'=>9),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, projectid, name, description, timestart, timeend, timecreated, timemodified, addressid, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		    // проект к которому привязано мероприятие
		    'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
		    // адрес, по которому проходит мероприятие
		    'address' => array(self::HAS_ONE, 'Address', 'addressid', 'condition' => "objecttype='event'"),
		    // Вакансии мероприятия
		    'vacancies' => array(self::HAS_MANY, 'EventVacancy', 'eventid'),
		    // активные вакансии мероприятия
		    'activevacancies' => array(self::HAS_MANY, 'EventVacancy', 'eventid', 'condition' => "status='active'"),
		    // Приглашения на мероприятие
		    'invites' => array(self::HAS_MANY, 'EventInvite', 'eventid'),
		    // Видео c мероприятия
		    'videos' => array(self::HAS_MANY, 'Video', 'objectid',
		        'condition' => "objecttype='projectevent'"),
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
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

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
	}
	
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
	    $criteria = new CDbCriteria();
	    $criteria->order = 'timestart ASC';
	    $events = array();
	    // Защита сервера от перегрузки: не даем запрашивать события бальше чем за 3 месяца
	    $defaultTimeStart = time();
	    $defaultTimeEnd   = $timeStart + 3*31*24*3600;
	    
	    $timeStart = intval($timeStart);
	    $timeEnd   = intval($timeEnd);
	    if ( ! $timeStart )
	    {
	        $timeStart = $defaultTimeStart;
	    }
	    if ( ! $timeEnd )
	    {
	        $timeEnd = $defaultTimeEnd;
	    }
	    if ( $timeEnd - $timeStart > 3*31*24*3600 )
	    {
	        $timeStart = $defaultTimeStart;
	        $timeEnd = $defaultTimeEnd;
	    }
	    $statuses = array('active', 'finished');
	    if ( $onlyActive )
	    {
	        $statuses = array('active');
	    }
	    
	    // составляем запрос к базе
	    // статус мероприятий
	    $criteria->addInCondition('status', $statuses);
	    // Временной отрезок (учитываем только время начала чтобы все влезло в календарь)
	    //$criteria->compare('timestart', '>='.$timeStart);
	    //$criteria->compare('timestart', '<='.$timeEnd);
	    // тип проекта
	    if ( $projectType )
	    {
	        $criteria->with = 'project';
	        if ( is_array($projectType) )
	        {
	            $criteria->addInCondition('project.type', $projectType);
	        }else
	       {
	            $criteria->compare('project.type', $projectType);
	        }
	    }
	    // id проекта
	    if ( $projectId )
	    {
	        $criteria->compare('projectid', $projectId);
	    }
	    
	    // id участника
	    if ( $userId )
	    {
	        if ( ! ( Yii::app()->getModule('user')->user()->id == $userId OR Yii::app()->user->isSuperuser ) )
	        {// разрешаем смотреть календарь пользователя только самому пользователю или админу
	            return;
	        }
	        
	        // оставляем только те события, в которых участвует пользователь
	        $recordset = $this->model()->findAll($criteria);
	        $records = array();
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
	 * @todo придумать как сделать события на несколько дней
	 * @todo убрать ссылку когда будет сделано всплывающее окно в календаре
	 */
	protected function convertEventsToCalendar($events)
	{
	    $result = array();
	    foreach ( $events as $event )
	    {
	        $instance = array();
	        $instance['id'] = $event->id;
	        $instance['title'] = $event->name;
	        $instance['allDay'] = false;
	        $instance['start'] = $event->timestart;
	        $instance['end'] = $event->timeend;
	        $instance['url'] =  Yii::app()->createUrl('//projects/event/view', array('id' => $event->id));
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
	            return array('active');
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
	 */
	public function setStatus($newStatus)
	{
	    if ( ! in_array($newStatus, $this->getAllowedStatuses()) )
	    {
	        return false;
	    }
	    
	    $this->status = $newStatus;
	    $this->save();
	    
	    $vacancies = $this->vacancies;
	     
	    if ( $newStatus == 'active' )
	    {// активируем все вакансии события
	        foreach ( $vacancies as $vacancy )
	        {
	            if ( $vacancy->status == 'draft' )
	            {
	                $vacancy->setStatus('active');
	            }
	        }
	    }
	     
	    if ( $newStatus == 'finished' )
	    {// закрываем все вакансии события
	        foreach ( $vacancies as $vacancy )
	        {
	            if ( $vacancy->status == 'active' )
	            {
	                $vacancy->setStatus('finished');
	            }
	            if ( $vacancy->status == 'draft' )
	            {// если событие завершено - удаляем все не начатые вакансии
	                $event->delete();
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
	 * Определить, закончилось ли мероприятие
	 * @return boolean
	 */
	public function getExpired()
	{
	    if ( $this->timeend < time() OR $this->status == 'finished' )
	    {
	        return true;
	    }
	    return false;
	}
}