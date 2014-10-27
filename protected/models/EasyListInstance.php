<?php

/**
 * Экземпляры списков пользователей
 * Эта модель позволяет связывать списки с любыми другими объектами в системе
 *
 * Таблица '{{user_list_instances}}':
 * @property integer $id
 * @property string $easylistid
 * @property string $objecttype
 * @property string $objectid
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property EasyList $easyList
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
class EasyListInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{easy_list_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('easylistid, objectid, timecreated, timemodified', 'length', 'max' => 11),
			array('objecttype', 'length', 'max' => 50),
			// The following rule is used by search().
			//array('id, easylistid, objecttype, objectid, timecreated', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'easyList' => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'EcTimestampBehavior' => array(
	            'class'           => 'application.behaviors.EcTimestampBehavior',
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
			'easylistid' => 'Userlistid',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
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
	/*public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('easylistid', $this->easylistid,true);
		$criteria->compare('objecttype', $this->objecttype,true);
		$criteria->compare('objectid', $this->objectid,true);
		$criteria->compare('timecreated', $this->timecreated,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}*/

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserListInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий:
	 * 
	 * @param string $objectType
	 * @param int    $objectId
	 * @return EasyListInstance
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий:
	 *
	 * @param string $objectType
	 * @param array $objectIds
	 * @return EasyListInstance
	 * 
	 * @deprecated
	 */
	public function forObjects($objectType, $objectIds)
	{
	    return $this->forObject($objectType, $objectId);
	}
}