<?php

/**
 * This is the model class for table "{{project_members}}".
 *
 * The followings are the available columns in table '{{project_members}}':
 * @property integer $id
 * @property string $memberid
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
 * @property User         $manager
 * @property EventVacancy $vacancy
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
     * (non-PHPdoc)
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.projects.ProjectsModule');
        
        // регистрируем обработчики событий
        // создание нового приглашения (участнику отправляется письмо)
        $this->attachEventHandler('onApprove', array('ProjectsModule', 'sendApproveMemberNotification'));
        $this->attachEventHandler('onReject', array('ProjectsModule', 'sendRejectMemberNotification'));
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
	 * (non-PHPdoc)
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    if ( $this->isNewRecord )
	    {// @todo при создании новой заявки сообщать участнику что она принята
	        
	        // автоматически переводим приглашение в статус "принято" если участник подал заявку на это мероприятие
	        $this->autoConfirmInvite();
	    }
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
		return array(
			array('memberid, vacancyid, timecreated, timemodified, managerid, timestart, timeend', 'length', 'max'=>11),
			array('request, responce', 'length', 'max'=>255),
			array('status', 'length', 'max'=>9),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, memberid, vacancyid, timecreated, timemodified, managerid, request, responce, timestart, timeend, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'member'  => array(self::BELONGS_TO, 'Questionary', 'memberid'),
		    'manager' => array(self::BELONGS_TO, 'User', 'managerid'),
		    'vacancy' => array(self::BELONGS_TO, 'EventVacancy', 'vacancyid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'memberid' => ProjectsModule::t('member_memberid'),
			'vacancyid' => ProjectsModule::t('vacancy'),
			'timecreated' => ProjectsModule::t('member_timecreated'),
			'timemodified' => ProjectsModule::t('timemodified'),
			'managerid' => ProjectsModule::t('member_managerid'),
			'request' => ProjectsModule::t('member_request'),
			'responce' => ProjectsModule::t('member_responce'),
			'timestart' => ProjectsModule::t('timestart'),
			'timeend' => ProjectsModule::t('timeend'),
			'status' => ProjectsModule::t('status'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	/*public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('memberid',$this->memberid,true);
		$criteria->compare('vacancyid',$this->vacancyid,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('managerid',$this->managerid,true);
		$criteria->compare('request',$this->request,true);
		$criteria->compare('responce',$this->responce,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}*/
	
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
	 * Автоматически подтвердить приглашение на участие в мероприятии.
	 * Выполняется только в том случае, если участник подал заявку, не просмотрев приглашения
	 * После подтверждения приглашение удаляется
	 * 
	 * @return null
	 */
	protected function autoConfirmInvite()
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('eventid', $this->vacancy->event->id);
	    $criteria->compare('questionaryid', $this->memberid);
	    
	    $invite = EventInvite::model()->find($criteria);
	    $invite->setStatus(EventInvite::STATUS_ACCEPTED);
	    $invite->deleted = 1;
	    $invite->save();
	}
}