<?php

/**
 * Модель "приглашение на съемки"
 * 
 * Таблица "{{event_invites}}":
 * @property integer $id
 * @property string $questionaryid
 * @property string $eventid
 * @property integer $deleted
 * @property string $timecreated
 * @property string $timemodified
 * @property string $status
 * @property string $subscribekey
 * 
 * Связи с другими таблицами:
 * @property Questionary  $questionary - анкета приглашенного участника
 * @property ProjectEvent $event - мероприятие
 * 
 * @todo заменить во всем коде модели текст статусов константами
 * @todo внедрить simpleWorkflow
 * @todo переписать delete() на мягкое удаление
 * @todo отказаться от поля delete
 * @todo изучить возможно ли объединение с таблицей customer_invites для того чтобы сделать 
 *       общую модель приглашений для всего приложения
 * 
 * Методы класса EcTimestampBehavior:
 * @method CActiveRecord createdBefore(int $time, string $operation='AND')
 * @method CActiveRecord createdAfter(int $time, string $operation='AND')
 * @method CActiveRecord updatedBefore(int $time, string $operation='AND')
 * @method CActiveRecord updatedAfter(int $time, string $operation='AND')
 * @method CActiveRecord modifiedOnly()
 * @method CActiveRecord neverModified()
 * @method CActiveRecord lastCreated()
 * @method CActiveRecord firstCreated()
 * @method CActiveRecord lastModified()
 * @method CActiveRecord firstModified()
 */
class EventInvite extends CActiveRecord
{
    /**
     * @var string - статус приглашения: ждет ответа участника
     *               Участник получил приглашение, но пока на него не ответил
     */
    const STATUS_PENDING    = 'pending';
    /**
     * @var string - статус приглашения: принято участником
     *               Участник получил приглашение, и принял согласился на участие в мероприятии
     *               Приглашение в таком статусе означает только сам факт согласия участвовать в съемках
     *               Пользователь может и не подать заявку на участие
     */
    const STATUS_ACCEPTED   = 'accepted';
    /**
     * @var string - статус приглашения: требуются дополнительные данные в заявке.
     *               Выставляется в случае если на проект подана заявка, но не все дополнительные
     *               поля были заполнены для дальнейшего отбора участника на роль (или отбора в кастинге)
     */
    const STATUS_INCOMPLETE = 'incomplete';
    /**
     * @var string - статус приглашения: отклонено
     *               Участник получил приглашение, и отказался участвовать в съемках
     *               Приглашение в таком статусе означает только сам факт отказа от участия в съемках
     *               Пользователь все равно может подать заявку на участие после отказа
     */
    const STATUS_REJECTED   = 'rejected';
    /**
     * @var string - статус приглашения: время истекло
     *               Участник получил приглашение, но не успел с ним ознакомиться,
     *               или слишком долго тупил с подачей заявки на участие
     */
    const STATUS_EXPIRED    = 'expired';
    /**
     * @var string - статус приглашения: отменено
     *               Участник получил приглашение, но вакансия была удалена (например из-за того что создана по ошибке)
     *               Этот статус используется редко
     */
    const STATUS_CANCELED   = 'canceled';
    /**
     * @var string - статус приглашения: удалено
     *               Служебный статус для удаленных записей
     */
    const STATUS_DELETED   = 'deleted';
    
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        parent::init();
        Yii::import('application.modules.projects.ProjectsModule');
        
        // регистрируем обработчики событий
        // создание нового приглашения (участнику отправляется письмо)
        $this->attachEventHandler('onNewInvite', array('ProjectsModule', 'sendNewInviteNotification'));
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EventInvite the static model class
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
		return '{{event_invites}}';
	}
	
	/**
	 * @see CActiveRecord::relations()
	 *
	 * @return array relational rules.
	 */
	public function relations()
	{
	    $relations = array(
	        // анкета приглашенного участника
	        'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
	        // мероприятие, на которое приглашен участник
	        'event'       => array(self::BELONGS_TO, 'ProjectEvent', 'eventid'),
	        /**
	         * {@todo одобренная заявка на участие в проекте
    	     *        (после создания таблицы связей приглашений с вакансиями)}
    	     * 'request'     => array(self::HAS_ONE, 'MemberRequest', array() ),
    	     * {@todo одобренная заявка на участие в проекте}
    	     * 'member'      => array(self::HAS_ONE, 'ProjectMember', array() ),
    	     */
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
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
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
	    );
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// при создании записи установим начальный статус
	        $this->status       = self::STATUS_PENDING;
	        $this->subscribekey = sha1(microtime().Yii::app()->params['hashSalt']);
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    if  ( $this->isNewRecord )
	    {// сразу же после создания приглашения - отсылаем письмо участнику
	        // этот процесс происходит при помощи события 'onNewInvite',
	        // которое перехватывается в классе ProjectsModule
	        $event = new CModelEvent($this);
	        $this->onNewInvite($event);
	    }
	    parent::afterSave();
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
	        // истекшие приглашения: все приглашения привязанные к прошедшим событиям
	        // (кроме приглашений на события без конкретной даты)
	        // @todo использовать with
	        'outdated' => array(
    	        'condition' => $this->getTableAlias(true).'.`event`.`timeend` < '.time().' AND '.
                               $this->getTableAlias(true).'.`event`.`nodates` = 0',
    	    ),
            // удаленные
            'deleted' => array(
    	        'condition' => $this->getTableAlias(true).'.`deleted` = 0',
            ),
            // не удаленные
            'notDeleted' => array(
    	        'condition' => $this->getTableAlias(true).'.`deleted` <> 0 AND '.
                    $this->getTableAlias(true).".`status` <> '".self::STATUS_DELETED."'",
            ),
	    );
        return CMap::mergeArray($timestampScopes, $modelScopes);
	}
	
	/**
	 * Именованая группа условий поиска: извлечь все записи с определенным статусом
	 * 
	 * @param  string|array $statuses
	 * @return EventInvite
	 */
	public function withStatus($statuses)
	{
	    if ( ! $statuses )
	    {// статус не указан - добавление условия не требуется
            return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`status`', $statuses);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все записи для участника или нескольких участников
	 * 
	 * @param int|array|Questionary $questionary
	 * @return EventInvite
	 */
	public function forQuestionary($questionary)
	{
	    if ( $questionary instanceof Questionary )
	    {
	        $questionary = $questionary->id;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`questionaryid`', $questionary);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все приглашения для мероприятия
	 * 
	 * @param  int|array|ProjectEvent $event
	 * @return EventInvite
	 */
	public function forEvent($event)
	{
	    if ( $event instanceof ProjectEvent )
	    {
	        $eventId = $event->id;
	    }else
	    {
	        $eventId = $event;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`eventid`', $eventId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * @see CActiveRecord::rules()
	 * 
	 * @return array validation rules for model attributes.
	 * 
	 * @todo проверять что статус находится в списке допустимых
	 * @todo проверки существования связанных записей
	 */
	public function rules()
	{
		return array(
			array('deleted', 'numerical', 'integerOnly' => true),
			array('questionaryid, eventid, timecreated, timemodified', 'length', 'max' => 11),
		    array('status', 'length', 'max' => 50),
		    array('subscribekey', 'length', 'max' => 40),
		);
	}

	/**
	 * @see CActiveRecord::attributeLabels()
	 * 
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'questionaryid' => ProjectsModule::t('user'),
			'eventid' => ProjectsModule::t('event'),
			'deleted' => Yii::t('coreMessages', 'deleted_sr'),
			'timecreated' => ProjectsModule::t('invite_timecreated'),
		    'timemodified' => Yii::t('coreMessages', 'timemodified'),
		    'status' => Yii::t('coreMessages', 'status'),
		);
	}
	
	/**
	 * Событие "создано новое приглашение"
	 * 
	 * @param  CModelEvent $event
	 * @return null
	 */
	public function onNewInvite($event)
	{
	    $this->raiseEvent('onNewInvite', $event);
	}
	
	/**
	 * Получить список статусов, в которые может перейти объект
	 * 
	 * @return array
	 * 
	 * @todo использовать simpleWorkflow
	 * @todo дополнить статусом deleted
	 */
	public function getAllowedStatuses()
	{
	    switch ( $this->status )
	    {
	        case self::STATUS_PENDING:
	            return array(
    	            self::STATUS_ACCEPTED,
    	            self::STATUS_INCOMPLETE,
    	            self::STATUS_REJECTED, 
    	            self::STATUS_EXPIRED, 
    	            self::STATUS_CANCELED,
	            );
	        case self::STATUS_ACCEPTED:
	            return array(
	               self::STATUS_REJECTED,
	               self::STATUS_INCOMPLETE,
	               self::STATUS_EXPIRED, 
	               self::STATUS_CANCELED,
	            );
	        case self::STATUS_INCOMPLETE:
	            return array(
	               self::STATUS_REJECTED, 
	               self::STATUS_ACCEPTED, 
	               self::STATUS_EXPIRED, 
	               self::STATUS_CANCELED,
	            );
	        case self::STATUS_REJECTED:
	            return array(
	               self::STATUS_PENDING,
	               self::STATUS_ACCEPTED,
	               self::STATUS_EXPIRED,
	               self::STATUS_CANCELED,
	            );
	        case self::STATUS_EXPIRED:
	            return array();
	        case self::STATUS_CANCELED:
	            return array();
	    }
	    return array();
	}
	
    /**
	 * Перевести объект из одного статуса в другой, выполнив все необходимые действия
	 * 
	 * @param  string $newStatus
	 * @param  bool   $saveNow - сразу же сохранить запись
	 * @return bool
	 * 
	 * @todo использовать simpleWorkflow
	 */
	public function setStatus($newStatus, $saveNow=true)
	{
	    if ( ! in_array($newStatus, $this->getAllowedStatuses()) )
	    {
	        return false;
	    }
	    $this->status = $newStatus;
	    if ( $saveNow )
	    {
	        return $this->save(true, array('status'));
	    }
	    return true;
	}
}