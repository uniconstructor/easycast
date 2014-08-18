<?php

/**
 * Заявка на участие в проекте, участник проекта, 
 * или история участия в проекте (в зависимости от статуса)
 *
 * Таблица '{{project_members}}':
 * @property integer $id
 * @property string $memberid - id анкеты участника (Questionary)
 *                              @todo переименовать в questionaryid
 * @property string $vacancyid
 * @property string $timecreated
 * @property string $timemodified
 * @property string $managerid
 * @property string $request
 * @property string $responce
 * @property string $timestart
 * @property string $timeend
 * @property string $status
 * 
 * Relations:
 * @property Questionary  $member
 * @property Questionary  $questionary
 * @property User         $manager
 * @property EventVacancy $vacancy
 * @property ProjectEvent $event
 * 
 * @todo внедрить workflow-модель: увеличить поле "статус" до 50 символов, обновить все старые записи
 *       переименовав статусы, наследовать от SWActiveRecord подключить swBehavior
 */
class ProjectMember extends CActiveRecord
{
    /**
     * @var string - статус заявки: черновик. Заявка подана участником и еще не рассмотрена.
     */
    const STATUS_DRAFT     = 'draft';
    /**
     * @var string - статус заявки: ждет решения режиссера. Заявка рассмотрена нами и предварительно отобрана.
     *               Этот статус используется в случае, когда актеров на мероприятие отбираем не мы, а другой
     *               человек (например режиссер).
     *               Заявка в этом статусе все еще может быть отклонена, но уже не нами, а режиссером.
     */
    const STATUS_PENDING   = 'pending';
    /**
     * @var string - статус заявки: одобрена. Заявка подана участником, рассмотрена и одобрена.
     *               Все участники с такими заявками считаются участниками проекта.
     */
    const STATUS_ACTIVE    = 'active';
    /**
     * @var string - статус заявки: отклонена. Заявка подана участником, рассмотрена и отклонена.
     */
    const STATUS_REJECTED  = 'rejected';
    /**
     * @var string - статус заявки: завершена. Заявка подана участником, рассмотрена и одобрена.
     *               Мероприятие завершилось.
     *               Этот статус отмечает только сам факт окончания участия в мероприятии.
     *               После окончания 
     */
    const STATUS_FINISHED  = 'finished';
    /**
     * @var string - статус заявки: отменена. Участник сам отменил свою заявку 
     */
    const STATUS_CANCELED  = 'canceled';
    /**
     * @var string - статус заявки: время истекло. Заявка была отменена автоматически, потому что участник
     *               слишком поздно ее подал или мы просто не успели ее обработать (мероприятие уже началось).
     */
    const STATUS_EXPIRED   = 'expired';
    /**
     * @var string - статус заявки: успешно завершена.
     *               Для кастинга: участник прошел кастинг
     *               Для остальных типов событий: участник пришел как и обещал (отмечена посещаемость)
     */
    const STATUS_SUCCEED   = 'succeed';
    /**
     * @var string - статус заявки: неуспешно завершена.
     *               Для кастинга: участник не прошел кастинг или не пришел на него
     *               Для остальных типов событий: участник не пришел на съемки
     */
    const STATUS_FAILED    = 'failed';
    
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.projects.ProjectsModule');
        // регистрируем обработчики событий
        // создание нового приглашения (участнику отправляется письмо)
        $this->attachEventHandler('onApprove', array('ProjectsModule', 'sendApproveMemberNotification'));
        $this->attachEventHandler('onReject', array('ProjectsModule', 'sendRejectMemberNotification'));
        $this->attachEventHandler('onPending', array('ProjectsModule', 'sendPendingMemberNotification'));
        
        parent::init();
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectMember the static model class
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
		return '{{project_members}}';
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        $criteria = new CDbCriteria();
	        $criteria->compare('memberid', $this->memberid);
	        $criteria->compare('vacancyid', $this->vacancyid);
	        if ( $this->exists($criteria) )
	        {// на одну и ту же роль нельзя подавать заявку 2 раза
	            return false;
	        }
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    if ( $this->isNewRecord )
	    {// @todo в зависимости от настроек оповещений: при создании новой заявки сообщать участнику что она принята
	        // автоматически переводим приглашение в статус "принято": 
	        // если участник подал заявку на это мероприятие, значит он соглаен участвовать
	        $this->autoConfirmInvite();
	        // привязываем заявку ко всем разделам роли чтобы потом было проще менять уже существующие записи
	        $this->createMemberInstances();
	    }
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	public function beforeDelete()
	{
	    foreach ( $this->instances as $instance )
	    {// удаляем все ссылки на заявку при удалении заявки
	        $instance->delete();
	    }
	    return parent::beforeDelete();
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('memberid, vacancyid, timecreated, timemodified, managerid, 
			    timestart, timeend', 'length', 'max' => 11),
			array('request, responce', 'length', 'max' => 4095),
			array('status', 'length', 'max' => 50),
			// The following rule is used by search().
			//array('id, memberid, vacancyid, timecreated, timemodified, managerid, request, responce, timestart, timeend, status', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
	    return array(
	        // участник проекта
	        // @todo неудачное название для связи: оставить только для совместимости, а затем удалить
	        // @todo заменить все использования связи member на questionary
	        'member'  => array(self::BELONGS_TO, 'Questionary', 'memberid'),
	        // участник проекта
	        'questionary' => array(self::BELONGS_TO, 'Questionary', 'memberid'),
	        // Сотрудник, одобривший или отклонивший заявку
	        'manager' => array(self::BELONGS_TO, 'User', 'managerid'),
	        // вакансия, на которую была подана заявка
	        'vacancy' => array(self::BELONGS_TO, 'EventVacancy', 'vacancyid'),
	        // мероприятие, на которое подана заявка
	        'event' => array(self::HAS_ONE, 'ProjectEvent', 'eventid', 'through' => 'vacancy'),
	
	        // все ссылки на эту заявку
	        'instances' => array(self::HAS_MANY, 'MemberInstance', 'memberid'),
	        // все разделы для заявок внутри роли (SectionInstances) в которых находится эта заявка
	        'sectionInstances' => array(self::MANY_MANY, 'CatalogSectionInstance',
	            "{{member_instances}}(memberid, objectid)",
	            'condition' => "`objecttype` = 'section_instance' AND `linktype` <> 'nolink'",
	        ),
	    );
	}
	
	/**
	 * @see parent::behaviors
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
	    return array(
	        'id'           => 'ID',
	        'memberid'     => ProjectsModule::t('member_memberid'),
	        'vacancyid'    => ProjectsModule::t('vacancy'),
	        'timecreated'  => ProjectsModule::t('member_timecreated'),
	        'timemodified' => ProjectsModule::t('timemodified'),
	        'managerid'    => ProjectsModule::t('member_managerid'),
	        'request'      => ProjectsModule::t('member_request'),
	        'responce'     => ProjectsModule::t('member_responce'),
	        'timestart'    => ProjectsModule::t('timestart'),
	        'timeend'      => ProjectsModule::t('timeend'),
	        'status'       => ProjectsModule::t('status'),
	    );
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return array(
    	    // только поданные заявки
    	    // @todo после введения условия withStatus больше не используется, удалить при рефакторинге
    	    'draft' => array(
    	        'condition' => "`status` = '".self::STATUS_DRAFT."'"
    	    ),
    	    // только подтвержденные заявки
	        // @todo после введения условия withStatus больше не используется, удалить при рефакторинге
    	    'active' => array(
    	        'condition' => "`status` = '".self::STATUS_ACTIVE."'"
    	    ),
    	    // только предварительно одобренные заявки
	        // @todo после введения условия withStatus больше не используется, удалить при рефакторинге
    	    'pending' => array(
    	        'condition' => "`status` = '".self::STATUS_PENDING."'"
    	    ),
    	    // только предварительно одобренные заявки
	        // @todo после введения условия withStatus больше не используется, удалить при рефакторинге
    	    'rejected' => array(
    	        'condition' => "`status` = '".self::STATUS_REJECTED."'"
    	    ),
    	    // только ждущие заявки (поданные или предварительно одобренные)
	        // @todo после введения условия withStatus больше не используется, удалить при рефакторинге
    	    'waiting' => array(
    	        'condition' => "`status` IN ('".self::STATUS_DRAFT."', '".self::STATUS_PENDING."')"
    	    ),
	        // последние поданные заявки
	        'lastCreated' => array(
    	        'order' => $this->getTableAlias(true).'.`timecreated` DESC'
    	    ),
	        // последние измененные заявки
	        'lastModified' => array(
    	        'order' => $this->getTableAlias(true).'.`timemodified` DESC'
    	    ),
	        // последние поданные заявки
	        'firstCreated' => array(
    	        'order' => $this->getTableAlias(true).'.`timecreated` ASC'
    	    ),
        );
	}
	
	/**
	 * Именованная группа условий поиска - выбрать заявки с определенными статусами
	 * @param array|string $statuses - статусы заявок, которые учитываются извлечении
	 *                                 если статус один - то его можно просто передать строкой
	 * @return ProjectMember
	 */
	public function withStatus($statuses=array(self::STATUS_ACTIVE, self::STATUS_FINISHED))
	{
	    $criteria = new CDbCriteria();
        if ( ! is_array($statuses) )
        {// нужен только один статус, и он передан строкой - сделаем из нее массив
            $statuses = array($statuses);
        }
        if ( empty($statuses) )
        {// Если статус не указан - берем все кроме отклоненных
            $criteria->compare($this->getTableAlias(true).'.`status`', '<>'.self::STATUS_REJECTED);
        }else
        {
            $criteria->addInCondition($this->getTableAlias(true).'.`status`', $statuses);
        }
        $this->getDbCriteria()->mergeWith($criteria);
        
        return $this;
	}
	
	/**
	 * Именованная группа условий поиска - получить заявки принадлежащие определенной роли
	 * @param int $vacancyId - id роли, на которую подана заявка
	 * @return ProjectMember
	 */
	public function forVacancy($vacancyId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('vacancyid', $vacancyId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - получить заявки принадлежащие одной анкете
	 * @param int $questionaryId - id роли, на которую подана заявка
	 * @return ProjectMember
	 */
	public function forQuestionary($questionaryId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('memberid', $questionaryId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
    /**
     * Именованная группа условий поиска - 
     * @param array $instanceIds
     * @param array $linkTypes
     * @return ProjectMember
     * 
     * @todo заменить на INNER JOIN с таблицей member_instances как в условии forObject
     */
	public function forSectionInstances($instanceIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->together = true;
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forSectionInstances' => array($instanceIds),
	            ),
	        ),
	    );
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
    /**
     * Именованная группа условий поиска - 
     * @param array $instanceIds
     * @param array $linkTypes
     * @return ProjectMember
     */
	public function forSectionInstance($instanceId, $linkTypes=array())
	{
	    if ( ! $linkTypes )
	    {
	        $linkTypes = array('nograde', 'good', 'normal', 'bad');
	    }
	    $criteria = new CDbCriteria();
	    $criteria->together = true;
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObject'     => array('section_instance', $instanceId),
	                'withLinkTypes' => array($linkTypes),
	            ),
	        ),
	    );
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
    /**
     * Именованная группа условий поиска - 
     * @param array $linkTypes
     * @return ProjectMember
     * 
     * @deprecated оценки анкет больше не используются
     */
	public function withLinkTypes($linkTypes)
	{
	    if ( ! $linkTypes )
	    {
	        $linkTypes = array('nograde', 'good', 'normal', 'bad');
	    }
	    $criteria = new CDbCriteria();
	    $criteria->together = true;
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'withLinkTypes' => array($linkTypes),
	            ),
	        ),
	    );
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все заявки, привязаные к определенному объекту
	 * @param string $objectType
	 * @param int $objectId
	 * @return ProjectMember
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->together = true;
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObject' => array($objectType, $objectId),
	            ),
	        ),
	    );
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все заявки, привязаные к нескольким объектам одного типа
	 * (например если нужно получить все заявки, которые находятся в нескольких вкладках роли)
	 * Примечание: здесь можно было бы применить несколько вызовов forObject и слить критерии через OR
	 *             но IN по id будет работать быстрее, потому что не нужно будет каждый раз проверять
	 *             objecttype для каждой связи
	 * @param string $objectType - тип объектов к которым привязаны заявки
	 * @param array  $objectIds  - массив id объектов к которым привязяны заявки
	 * @return ProjectMember
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->together = true;
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObjects' => array($objectType, $objectIds),
	            ),
	        ),
	    );
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска
	 * 
	 * @param EventVacancy $vacancy
	 * @return ProjectMember
	 * 
	 * @todo 
	 */
	public function needsAllocation($vacancy)
	{
	    die ('UNFINISHED');
	    // сужаем выбоорку до заявок одной роли
	    $this->forVacancy($vacancy->id);
	    
	    if ( $vacancy->catalogSectionInstances )
	    {// получаем все разделы заявок из роли и смотрим чтобы участник не присутствовал ни в одной из них
	        $csids = array();
	        foreach ( $vacancy->catalogSectionInstances as $instance )
	        {
	            $csids[] = $instance->id;
	        }
	        // Окей, а что значит "заявка не присутствовует ни в одной из вкладок"?
	        // В нашем случае, выражаясь терминами базы данных
	        // это значит что эта заявка (ProjectMember) не имеет ссылок (MemberInstance)
	        // на перечисленные вкладки роли (CatalogSectionInstance)
	        // либо все эти ссылки означают отсутсвие заявки в разделе (тип связи 'nolink' в MemberInstance)
	        $criteria = new CDbCriteria();
	        $criteria->together = true;
	        $criteria->with = array(
    	        'instances' => array(
    	            'select'   => 'id',
    	            'joinType' => 'INNER JOIN',
    	            'scopes'   => array(
    	                //'havingCount'   => 0,
    	                'forObjects'    => array('section_instance', $csids),
    	                'withLinkTypes' => array(array('nolink')),
    	            ),
    	        ),
    	    );
	        $this->getDbCriteria()->mergeWith($criteria);
	    }
	    
	    return $this;
	}
	
	/**
	 * Получить все доступные заявки, которые можно поместить хотя бы в одну группу 
	 * 
	 * @param EventVacancy $vacancy
	 * @return array
	 */
	public function withAvailableSections($vacancy, $minCount=0)
	{
	    if ( $vacancy->catalogSectionInstances )
	    {// получаем все разделы заявок из роли и смотрим чтобы участник не присутствовал ни в одной из них
    	    $instanceIds = array();
    	    foreach ( $vacancy->catalogSectionInstances as $sectionInstance )
    	    {
    	        $instanceIds[] = $sectionInstance->id;
    	    }
    	    $criteria = new CDbCriteria();
    	    $criteria->together = true;
    	    $criteria->with = array(
    	        'instances' => array(
    	            'select'   => false,
    	            'joinType' => 'INNER JOIN',
    	            'scopes'   => array(
    	                'forObjects'    => array('section_instance', $instanceIds),
    	                'withLinkTypes' => array(array('nolink')),
    	            ),
    	        ),
    	    );
    	    $this->getDbCriteria()->mergeWith($criteria);
	    }
	    return $this;
	}
	
	/**
	 * Получить все незаблокированные записи
	 * @return ProjectMember
	 */
	public function unlocked()
	{
	    // получаем все заблокированные объекты
	    if ( ! $locks = ObjectLock::model()->forObjectType('project_member')->getIds() )
	    {
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->addNotInCondition($this->getTableAlias().'.`id`', $locks);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Получить все незаблокированые записи, + записи которые блокированы определенным пользователем
	 * (чтобы не спотыкаться об свою собственную блокировку)
	 * @param string $lockerType
	 * @param int $lockerId
	 * @return ProjectMember
	 */
	public function unlockedFor($lockerType, $lockerId)
	{
	    $locks = ObjectLock::model()->forObjectType('project_member')->skipLockedBy($lockerType, $lockerId)->getIds();
	    if ( ! $locks )
	    {
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->addNotInCondition($this->getTableAlias().'.`id`', $locks);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Событие "заявка предварительно отобрана"
	 * @param CModelEvent $event
	 * @return null
	 */
	public function onPending($event)
	{
	    $this->raiseEvent('onPending', $event);
	}
	
	/**
	 * Событие "заявка подтверждена"
	 * @param CModelEvent $event
	 * @return null
	 */
	public function onApprove($event)
	{
	    $this->raiseEvent('onApprove', $event);
	}
	
	/**
	 * Событие "заявка подтверждена"
	 * @param CModelEvent $event
	 * @return null
	 */
	public function onReject($event)
	{
	    $this->raiseEvent('onReject', $event);
	}
	
	/**
	 * Событие "заявка отменена участником"
	 * @param CModelEvent $event
	 * @return null
	 */
	public function onCancel($event)
	{
	    $this->raiseEvent('onCancel', $event);
	}
	
	/**
	 * Событие "заявка успешно завершена"
	 * @param CModelEvent $event
	 * @return null
	 */
	public function onSucceed($event)
	{
	    $this->raiseEvent('onSucceed', $event);
	}
	
	/**
	 * Событие "заявка неуспешно завершена"
	 * @param CModelEvent $event
	 * @return null
	 */
	public function onFailed($event)
	{
	    $this->raiseEvent('onFailed', $event);
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
	        // черновик
	        case self::STATUS_DRAFT:
	            return array(
	                self::STATUS_PENDING,
	                self::STATUS_ACTIVE,
	                self::STATUS_REJECTED,
	                self::STATUS_CANCELED,
	            );
            break;
            // предварительно одобрена
            case self::STATUS_PENDING:
                return array(
                    self::STATUS_ACTIVE,
                    self::STATUS_REJECTED,
                );
            break;
            // одобрена
	        case self::STATUS_ACTIVE:
	            return array(
	                self::STATUS_FINISHED,
	                self::STATUS_REJECTED,
	            );
            break;
            // отклонена
            case self::STATUS_REJECTED:
                return array(self::STATUS_ACTIVE);
            break;
            // отменена
            case self::STATUS_CANCELED:
                return array(self::STATUS_DRAFT);
            break;
            // завершена
            case self::STATUS_FINISHED:
                return array(
                    self::STATUS_SUCCEED,
                    self::STATUS_FAILED,
                );
            break;
	    }
        // переход из этого статуса недоступен
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
	    return ProjectsModule::t('member_status_'.$status);
	}
	
	/**
	 * Установить новый статус
	 * 
	 * @param string $newStatus
	 * @return boolean
	 */
    public function setStatus($newStatus)
	{
	    if ( $this->status == $newStatus )
	    {// статус не надо менять
	        return true;
	    }
	    if ( ! in_array($newStatus, $this->getAllowedStatuses()) )
	    {// недопустимый статус
	        return false;
	    }
	    
	    $this->status = $newStatus;
	    $this->save();
	    
	    if ( $newStatus == self::STATUS_PENDING )
	    {// заявка предварительно одобрена
	        $event = new CModelEvent($this);
	        $this->onPending($event);
	    }elseif ( $newStatus == self::STATUS_ACTIVE )
	    {// заявка одобрена - отправляем письмо
	        $event = new CModelEvent($this);
	        $this->onApprove($event);
	    }elseif ( $newStatus == self::STATUS_REJECTED )
	    {// заявка отклонена - отправляем письмо
	        $event = new CModelEvent($this);
	        $this->onReject($event);
	    }elseif ( $newStatus == self::STATUS_CANCELED )
	    {// заявка отменена участником - отправляем письмо с подтверждением
	        // @todo создать письмо для этого случая
	        $event = new CModelEvent($this);
	        $this->onCancel($event);
	    }
	    return true;
	}
	
	/**
	 * Добавить заявку в раздел роли
	 * @param int $instanceId - id ссылки на раздел роли (CatalogSectionInstance)
	 * @return boolean
	 */
	public function addToInstance($instanceId)
	{
	    $existedLink = MemberInstance::model()->forMember($this->id)->
	       forSectionInstance($instanceId)->exists();
	    if ( $existedLink )
	    {
	        return true;
	    }
	    $link = new MemberInstance();
	    $link->memberid   = $this->id;
	    $link->objecttype = 'section_instance';
	    $link->objectid   = $instanceId;
	    return $link->save();
	}
	
	/**
	 * Убрать заявку из раздела роли
	 * @param int $instanceId - id ссылки на раздел роли (CatalogSectionInstance)
	 * @return boolean
	 */
	public function removeFromInstance($instanceId)
	{
	    return MemberInstance::model()->forMember($this->id)->
	       forSectionInstance($instanceId)->delete();
	}
	
	/**
	 * Автоматически подтвердить приглашение на участие в мероприятии.
	 * Выполняется только в том случае, если участник подал заявку, не просмотрев приглашение в анкете
	 * После подтверждения приглашение удаляется
	 * 
	 * @return null
	 */
	protected function autoConfirmInvite()
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('eventid', $this->vacancy->event->id);
	    $criteria->compare('questionaryid', $this->memberid);
	    
	    if ( ! $invite = EventInvite::model()->find($criteria) )
	    {// когда рассылались приглашения пользователя еще не было в базе - это нормально
	        return true;
	    }
	    $invite->setStatus(EventInvite::STATUS_ACCEPTED);
	    $invite->deleted = 1;
	    
	    return $invite->save(false);
	}
	
	/**
	 * Автоматически создать связи со существующими разделами заявок в роли после подачи заявки
	 * @return void
	 * 
	 * @deprecated связь 'nolink' больше не используется - этот метод больше не нужен
	 */
	protected function createMemberInstances()
	{
	    if ( ! $this->vacancy->catalogSectionInstances )
	    {// в роли нет разделов для отбора заявок - действия не требуются
	        return;
	    }
	    foreach ( $this->vacancy->catalogSectionInstances as $sectionInstance )
	    {// получаем все разделы заявок из роли и привязываем заявку к каждому из них как черновик
	        $memberInstance = new MemberInstance();
	        $memberInstance->objecttype = 'section_instance';
	        $memberInstance->objectid   = $sectionInstance->id;
	        $memberInstance->memberid   = $this->id;
	        $memberInstance->sourcetype = 'system';
	        $memberInstance->sourceid   = 0;
	        $memberInstance->status     = 'active';
	        $memberInstance->linktype   = 'nolink';
	        $memberInstance->save();
	    }
	}
}