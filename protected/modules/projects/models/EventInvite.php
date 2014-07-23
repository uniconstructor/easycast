<?php

/**
 * Модель "приглашение на съемки"
 * Таблица "{{event_invites}}".
 *
 * The followings are the available columns in table '{{event_invites}}':
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
 * @property Questionary $questionary - анкета приглашенного участника
 * @property ProjectEvent $event - мероприятие
 * 
 * @todo заменить во всем коде модели текст статусов константами
 * @todo переписать delete() на мягкое удаление
 */
class EventInvite extends CActiveRecord
{
    /**
     * @var string - статус приглашения: ждет ответа
     *               Участник получил приглашение, но пока на него не ответил
     */
    const STATUS_PENDING  = 'pending';
    /**
     * @var string - статус приглашения: принято
     *               Участник получил приглашение, и принял согласился на участие в мероприятии
     *               Приглашение в таком статусе означает только сам факт согласия участвовать в съемках
     *               Пользователь может и не подать заявку на участие
     */
    const STATUS_ACCEPTED = 'accepted';
    /**
     * @var string - статус приглашения: отклонено
     *               Участник получил приглашение, и отказался участвовать в съемках
     *               Приглашение в таком статусе означает только сам факт отказа от участия в съемках
     *               Пользователь все равно может подать заявку на участие после отказа
     */
    const STATUS_REJECTED = 'rejected';
    /**
     * @var string - статус приглашения: время истекло
     *               Участник получил приглашение, но не успел с ним ознакомиться,
     *               или слишком долго тупил с подачей заявки на участие
     */
    const STATUS_EXPIRED  = 'expired';
    /**
     * @var string - статус приглашения: отменено
     *               Участник получил приглашение, но вакансия была удалена (например из-за того что создана по ошибке)
     *               Этот статус используется редко
     */
    const STATUS_CANCELED = 'canceled';
    
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
	 * @return EventInvites the static model class
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
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        )
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
	    return array(
	        // истекшие приглашения: все приглашения привязанные к прошедшим событиям
	        // (кроме приглашений на события без конкретной даты)
	        'outdated' => array(
    	        'condition' => $this->getTableAlias(true).'.`event`.`timeend` < '.time().' AND '.
                               $this->getTableAlias(true).'.`event`.`nodates` = 0',
    	    ),
	        // последние созданные записи
	        'lastCreated' => array(
	            'order' => $this->getTableAlias(true).'.`timecreated` DESC'
	        ),
	        // последние измененные записи
	        'lastModified' => array(
	            'order' => $this->getTableAlias(true).'.`timemodified` DESC'
	        ),
	    );
	}
	
	/**
	 * Именованая группа условий поиска: извлечь все записи с определенным статусом
	 * @param array $statuses
	 * @return EventInvite
	 */
	public function withStatus($statuses)
	{
	    $criteria = new CDbCriteria();
	    
	    if ( ! is_array($statuses) )
	    {// нужен только один статус, и он передан строкой - сделаем из нее массив
	       $statuses = array($statuses);
	    }
	    if ( empty($statuses) )
	    {// статус не указан - добавление условия не требуется
            return $this;
	    }
	    
	    $criteria->addInCondition($this->getTableAlias(true).'.`status`', $statuses);
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 * 
	 * @todo проверять что статус находится в списке допустимых
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
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // анкета приглашенного участника
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    // мероприятие, на которое приглашен участник
		    'event'       => array(self::BELONGS_TO, 'ProjectEvent', 'eventid'),
		    
		    /** 
		     * {@todo одобренная заявка на участие в проекте 
		     *     (после создания таблицы связей приглашений с вакансиями)}
		     * 'request'     => array(self::HAS_ONE, 'MemberRequest', array() ),
		     * {@todo одобренная заявка на участие в проекте}
		     * 'member'      => array(self::HAS_ONE, 'ProjectMember', array() ),
		     */
		);
	}

	/**
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
	 * @param CModelEvent $event
	 * @return null
	 */
	public function onNewInvite($event)
	{
	    $this->raiseEvent('onNewInvite', $event);
	}
	
	/**
	 * Получить список статусов, в которые может перейти объект
	 * @return array
	 */
	public function getAllowedStatuses()
	{
	    switch ( $this->status )
	    {
	        case self::STATUS_PENDING:
	            return array(self::STATUS_ACCEPTED, self::STATUS_REJECTED, self::STATUS_EXPIRED, self::STATUS_CANCELED);
	            break;
	        case self::STATUS_ACCEPTED:
	            return array(self::STATUS_REJECTED, self::STATUS_EXPIRED, self::STATUS_CANCELED);
	            break;
	        case self::STATUS_REJECTED:
	            return array(self::STATUS_ACCEPTED, self::STATUS_EXPIRED, self::STATUS_CANCELED);
	            break;
	        case self::STATUS_EXPIRED:
	            return array();
	            break;
	        case self::STATUS_CANCELED:
	            return array();
	            break;
	    }
	
	    return array();
	}
	
    /**
	 * Перевести объект из одного статуса в другой, выполнив все необходимые действия
	 * @param string $newStatus
	 * @return bool
	 * 
	 * @todo вынести в behaviour
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
}