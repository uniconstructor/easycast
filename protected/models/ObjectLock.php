<?php

/**
 * This is the model class for table "{{locked_objects}}".
 *
 * The followings are the available columns in table '{{locked_objects}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $lockertype
 * @property string $lockerid
 * @property string $timecreated
 * @property string $timemodified
 * @property string $expire
 * 
 * @todo переименовать таблицу в object_locks
 */
class ObjectLock extends CActiveRecord
{
    /**
     * @var int - количество секунд на которое блокируется объект если время блокировки не задано
     */
    const DEFAULT_LOCK_TIME = 300;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{locked_objects}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype, lockertype', 'length', 'max' => 50),
			array('objectid, lockerid, timecreated, timemodified, expire', 'length', 'max' => 11),
		    array('expire', 'default', 'setOnEmpty' => true, 'value' => time() + self::DEFAULT_LOCK_TIME),
		    
			// The following rule is used by search().
			array('id, objecttype, objectid, lockertype, lockerid, timecreated, timemodified, expire', 
			    'safe', 'on' => 'search',
            ),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания
	        'EcTimestampBehavior' => array(
	            'class' => 'application.behaviors.EcTimestampBehavior',
	        ),
	    );
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'lockertype' => 'Lockertype',
			'lockerid' => 'Lockerid',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'expire' => 'Expire',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('lockertype',$this->lockertype,true);
		$criteria->compare('lockerid',$this->lockerid,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('expire',$this->expire,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ObjectLock the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий
	 * @param string $objectType
	 * @return ObjectLock
	 */
	public function forObjectType($objectType)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованая группа условий
	 * @param string $objectType
	 * @param int    $objectId
	 * @return ObjectLock
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->compare('objectid', $objectId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Найти все блокировки установленные определенным объектом (например пользователем)
	 * @param string $objectType
	 * @param int    $objectId
	 * @return ObjectLock
	 */
	public function lockedBy($lockerType, $lockerId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('lockertype', $objectType);
	    $criteria->compare('lockerid', $objectId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Игнорировать все блокировки установленные определенным объектом 
	 * (нужно чтобы не натыкаться на собственные блокировки)
	 * @param string $objectType
	 * @param int    $objectId
	 * @return ObjectLock
	 */
	public function skipLockedBy($lockerType, $lockerId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('lockertype', $lockerType);
	    $criteria->compare('lockerid', '<>'.$lockerId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Заблокировать объект
	 * 
	 * @param string $objectType
	 * @param int    $objectId
	 * @param int    $period
	 * @param string $lockerType - тип объекта создавшего блокировку (например user)
	 * @param int    $lockerId - id объекта создавшего блокировку
	 * @return bool
	 */
	public function lock($objectType, $objectId, $period, $lockerType='system', $lockerId=0)
	{
	    if ( $this->forObject($objectType, $objectId)->exists() )
	    {// нельзя блокировать два раза один и тот же объект
	        return false;
	    }
	    $lock = new ObjectLock();
	    $lock->objecttype = $objectType;
	    $lock->objectid   = $objectId;
	    $lock->lockertype = $lockerType;
	    $lock->lockerid   = $lockerId;
	    $lock->expire     = time() + $period;
	    
	    return $lock->save();
	}
	
	/**
	 * Снять блокировку
	 * 
	 * @param string $objectType
	 * @param int    $objectId
	 * @param string $lockerType - тип объекта снявшего блокировку (например user)
	 * @param int    $lockerId - id объекта снявшего блокировку 
	 *                           (информация о том кто снял блокировку нужна в основном 
	 *                           для логов и статистики, поэтому пока не используется)
	 * @return bool
	 */
	public function unlock($objectType, $objectId, $unlockerType='system', $unlockerId=0)
	{
	    if ( ! $this->forObject($objectType, $objectId)->exists() )
	    {// блокировка уже снята - действия не требуются
	        return true;
	    }
	    // снимаем блокировку с объекта
	    return $this->forObject($objectType, $objectId)->deleteAll();
	}
	
	/**
	 * Заблокировать объект от имени текущего пользователя
	 * 
	 * @param string $objectType
	 * @param int    $objectId
	 * @param int    $period
	 * @param string $lockerType
	 * @param int    $lockerId
	 * @return bool
	 */
	public function lockByUser($objectType, $objectId, $period)
	{
	    if ( ! $userId = Yii::app()->user->id )
	    {
	        $userId = 0;
	    }
	    return $this->lockObject($objectType, $objectId, $period, 'user', $userId);
	}
	
	/**
	 * Очистить устаревшие блокировки
	 * @return void
	 */
	public function clearLocks()
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('expire', '<'.time());
	    
	    $this->deleteAll($criteria);
	}
	
	/**
	 * Получить список id заблокированных объектов
	 * @param CDbCriteria $criteria
	 * @return array
	 */
	public function getIds($criteria=null)
	{
	    $result = array();
	    if ( $criteria )
	    {
	        $this->getDbCriteria()->mergeWith($criteria);
	    }
	    if ( ! $locks = $this->findAll() )
	    {
	        return array();
	    }
	    foreach ( $locks as $lock )
	    {
	        $result[$lock->objectid] = $lock->objectid;
	    }
	    return $result;
	}
}