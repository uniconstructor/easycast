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
 */
class ProjectMember extends CActiveRecord
{
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FINISHED = 'finished';
    
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
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		    'member' => array(self::BELONGS_TO, 'Questionary', 'memberid'),
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
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

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
	            return array('active', 'rejected');
            break;
	        case 'active':
	            return array('finished', 'rejected');
            break;
            case 'rejected':
                return array('active');
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