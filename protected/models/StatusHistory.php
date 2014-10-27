<?php

/**
 * Модель для работы с историей изменения статусов объекта
 * Используется для всех объектов у которых есть статус
 *
 * Таблица '{{status_history}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $oldstatus
 * @property string $newstatus
 * @property string $timecreated
 * @property string $sourcetype
 * @property string $sourceid
 * 
 * @todo подключить CustomRelationSourceBehavior
 * @todo дописать недостающие условия поиска
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
			array('sourcetype, objecttype, oldstatus, newstatus', 'length', 'max' => 50),
			array('objectid, timecreated, sourceid', 'length', 'max' => 11),
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
	        // автоматическое заполнение дат создания и изменения
	        'EcTimestampBehavior' => array(
	            'class' => 'application.behaviors.EcTimestampBehavior',
	            'updateAttribute' => null,
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
			'oldstatus' => 'Oldstatus',
			'newstatus' => 'Newstatus',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return StatusHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий: получить всю историю изменения статусов по одному объекту
	 * 
	 * @param  string $objectType
	 * @param  int    $objectId
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
	 * 
	 * @return StatusHistory|null
	 */
	public function getLastItem()
	{
	    $criteria = new CDbCriteria();
	    $criteria->order = $this->getTableAlias(true).".`timecreated` DESC";
	    $criteria->limit = 1;
	    if ( ! $result = $this->findAll($criteria) )
	    {
	        return;
	    }
	    return current($result);
	}
	
	/**
	 * Получить название объекта, который дал команду изменить статус
	 * (например имя пользователя который запустил мероприятие)
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
