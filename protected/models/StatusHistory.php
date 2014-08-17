<?php

/**
 * This is the model class for table "{{status_history}}".
 *
 * The followings are the available columns in table '{{status_history}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $oldstatus
 * @property string $newstatus
 * @property string $timecreated
 * @property string $sourceid
 * @property string $sourcetype
 */
class StatusHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{status_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype, oldstatus, newstatus', 'required'),
			array('sourcetype, objecttype, oldstatus, newstatus', 'length', 'max'=>50),
			array('objectid, timecreated, sourceid', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, objecttype, objectid, oldstatus, newstatus, timecreated, sourceid', 'safe', 'on'=>'search'),
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
	        'CTimestampBehavior' => array(
	            'class'            => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	        ),
	    );
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
			'oldstatus' => 'Oldstatus',
			'newstatus' => 'Newstatus',
			'timecreated' => 'Timecreated',
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
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('oldstatus', $this->oldstatus, true);
		$criteria->compare('newstatus', $this->newstatus, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('sourceid', $this->sourceid, true);
		$criteria->compare('sourcetype', $this->sourcetype, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StatusHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий: получить всю историю изменения статусов по одному объекту
	 * @param string $objectType
	 * @param int    $objectId
	 * @return StatusHistory
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
	 * Именованая группа условий: получить последнюю запись об изменении объекта если она есть
	 * @return StatusHistory|null
	 */
	public function getLastItem()
	{
	    $criteria = new CDbCriteria();
	    $criteria->order = $this->getTableAlias().".`timecreated` DESC";
	    $criteria->limit = 1;
	    if ( ! $result = $this->findAll($criteria) )
	    {
	        return;
	    }
	    return current($result);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getSourceName()
	{
	    switch ( $this->sourcetype )
	    {
	        case 'customer_invite':
	            if ( $source = CustomerInvite::model()->findByPk($this->sourceid) )
	            {
	                return $source->name;
	            }
	        break;
	    }
	    return '';
	}
}
