<?php

/**
 * This is the model class for table "{{event_vacancies}}".
 *
 * The followings are the available columns in table '{{event_vacancies}}':
 * @property integer $id
 * @property string $eventid
 * @property string $name
 * @property string $description
 * @property string $scopeid - id условия поиска, по которому будут искаться участники на вакансию
 * @property string $limit - количество человек в вакансии
 * @property string $timecreated
 * @property string $timemodified
 * @property string $status
 */
class EventVacancy extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EventVacancy the static model class
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
		return '{{event_vacancies}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    // При вакансии удаляем всех соискателей и участников этой вакансии
	    $members = ProjectMember::model()->findAll('vacancyid = '.$this->id);
	    foreach ( $members as $member )
	    {
	        $member->delete();
	    }
	}
	
	/**
	 * @see parent::behaviors
	 */
	public function behaviors()
	{
	    $searchConfig = array(
                'class' => 'ext.ESearchScopes.behaviors.ESearchScopeBehavior',
	            'idAttribute' => 'scopeid',
	            
            );
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	        'ESearchScopeBehavior' => $searchConfig,
	    );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => 'timecreated DESC');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, limit', 'required'),
			array('eventid, scopeid, timecreated, timemodified', 'length', 'max'=>11),
			array('name, description', 'length', 'max'=>255),
			array('limit', 'length', 'max'=>6),
			array('status', 'length', 'max'=>9),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, eventid, name, description, scopeid, limit, timecreated, timemodified, status', 'safe', 'on'=>'search'),
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
		    // мероприятие на которое создана вакансия
		    'event' => array(self::BELONGS_TO, 'ProjectEvent', 'eventid'),
		    // критерий поиска, по которому выбираются подходящие на вакансию участники 
		    'scope' => array(self::BELONGS_TO, 'SearchScope', 'scopeid'),
		    // Заявки на участие
		    'requests' => array(self::HAS_MANY, 'MemberRequest', 'vacancyid'),
		    // одобренные заявки на вакансию
		    'members' => array(self::HAS_MANY, 'ProjectMember', 'vacancyid', 'condition' => "status='active' OR status ='finished'"),
		    // отклоненные заявки на вакансию
		    'rejectedmembers' => array(self::HAS_MANY, 'ProjectMember', 'vacancyid', 'condition' => "status='rejected'"),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'eventid' => ProjectsModule::t('еvent'),
			'name' => ProjectsModule::t('name'),
			'description' => ProjectsModule::t('description'),
			'scopeid' => ProjectsModule::t('vacancy_scopeid'),
			'limit' => ProjectsModule::t('vacancy_limit'),
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
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
		$criteria->compare('eventid',$this->eventid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('scopeid',$this->scopeid,true);
		$criteria->compare('limit',$this->limit,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Разослать приглашения всем подходящим пользователям
	 * @return bool
	 * 
	 * @todo выбирать пользователей по критерию
	 */
	public function sendInvites()
	{
	    $criteria = new CDbCriteria();
	    $criteria->select = 'id';
	    
	    $users = Questionary::model()->findAll($criteria);
	    foreach ( $users as $user )
	    {// перебираем всех подходящих пользователей
	        if ( $this->isInvited($user->id, $this->event->id) )
	        {// проверяем, приглашен ли пользователь на мероприятие
	            continue;
	        }
	        $invite = new EventInvite;
	        $invite->questionaryid = $user->id;
	        $invite->eventid = $this->event->id;
	        //$invite->vacancyid = $this->id;
	        $invite->save();
	    }
	    
	    return true;
	}
	
	/**
	 * Проверить, приглашен ли пользователь на мероприятие
	 * @param int $questionaryId - id анкеты участника в таблице questionary
	 * @param int $eventId
	 * @return boolean
	 */
	protected function isInvited($questionaryId, $eventId)
	{
	    $condition = 'eventid = :eventid AND questionaryid = :userid';
	    $params = array(':eventid' => $eventId, ':userid' => $questionaryId);
	    return EventInvite::model()->exists($condition, $params);
	}
	
	/**
	 * Может ли пользователь претендовать на эту вакансию?
	 * @param int $questionaryId - id анкеты пользователя в таблице questionary
	 * @param bool $ignoreApplication - считать вакансию доступной, даже если на нее уже подана заявка
	 * @return bool
	 * 
	 * @todo добавить сравнение по критерию
	 */
	public function isAvailableForUser($questionaryId=null, $ignoreApplication=false)
	{
	    if ( ! Yii::app()->user->checkAccess('User') )
	    {// вакансии могут быть доступны только для участников
	        return false;
	    }
	    
	    if ( ! $questionaryId )
	    {// id анкеты не указан - попробуем взять текущий
	        $questionaryId = $this->getCurrentUserQuestionaryId();
	    }
	    
	    if ( ! $ignoreApplication AND $this->hasApplication($questionaryId) )
	    {// участник уже подал заявку на эту вакансию
	        return false;
	    }
	    
	    if ( ! $this->userMatchVacancyConditions($questionaryId) )
	    {// участник не подходит под указанные в вакансии критерии
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * Определить, посылал ли уже участник заявку на эту вакансию
	 * @param int $questionaryId - id анкеты пользователя в таблице questionary
	 * @param array $statuses - статусы заявки. Можно выборочно учитывать только ожидающие (draft), 
	 *                           подтвержденные (active), отклоненные (rejected) или завершенные (finished) заявки
	 *                           А также любые комбинации этих статусов
	 * @return boolean
	 */
	public function hasApplication($questionaryId=null, $statuses=null)
	{
	    if ( ! $questionaryId )
	    {// id анкеты не указан - попробуем взять текущий
	        $questionaryId = $this->getCurrentUserQuestionaryId();
	    }
	    $criteria = new CDbCriteria();
	    $criteria->addCondition('memberid=:memberid');
	    $criteria->addCondition('vacancyid=:vacancyid');
	    $criteria->params = array(':memberid' => $questionaryId, ':vacancyid' => $this->id);
	    
	    if ( is_array($statuses) AND ! empty($statuses) )
	    {
	        $criteria->addInCondition('status', $statuses);
	    }
	    
	    return ProjectMember::model()->exists($criteria);
	}
	
	/**
	 * Определить, подходит ли участник по условиям, указанным в вакансии
	 * 
	 * @param int $questionaryId - id анкеты пользователя 
	 * @return bool
	 * 
	 * @todo сам алгоритм сравнения разместить в questionary
	 */
	protected function userMatchVacancyConditions($questionaryId)
	{
	    return false;
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
	            if ( $this->event->status == 'active' )
	            {
	                return array('active');
	            }
            break;
	        case 'active':
                return array('finished');
            break;
            case 'finished':
                if ( in_array($this->event->status, array('active', 'draft')) )
                {
                    return array('active');
                }
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
	    return ProjectsModule::t('vacancy_status_'.$status);
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
	
	    return true;
	}
	
	/**
	 * Определить id анкеты текущего пользователя (если он участник)
	 * 
	 * @return int
	 * 
	 * @todo перенести эту функцию в другую библиотеку
	 */
	protected function getCurrentUserQuestionaryId()
	{
	    if ( Yii::app()->getModule('user')->user() )
	    {
	        $questionaryId = Yii::app()->getModule('user')->user()->questionary->id;
	    }else
	    {// что-то не так с учетной записью
	        // @todo записать ошибку в лог
	        return 0;
	    }
	}
}