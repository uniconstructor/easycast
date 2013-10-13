<?php

/**
 * Модель для работы с отчетами
 *
 * Таблица '{{reports}}':
 * @property integer $id
 * @property string $authorid
 * @property string $name
 * @property string $type
 * @property string $timecreated
 * @property string $timemodified
 * @property string $plantime
 * @property string $data
 * @property string $status
 */
class Report extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Report the static model class
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
		return '{{reports}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('authorid, timecreated, timemodified, plantime', 'length', 'max'=>11),
			array('name', 'length', 'max'=>255),
			array('type', 'length', 'max'=>20),
			array('status', 'length', 'max'=>50),
			array('data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, authorid, name, type, timecreated, timemodified, plantime, data, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'links' => array(self::HAS_MANY, 'ReportLink', 'reportid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'authorid' => 'Authorid',
			'name' => 'Name',
			'type' => 'Type',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'plantime' => 'Plantime',
			'data' => 'Data',
			'status' => 'Status',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('authorid',$this->authorid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('plantime',$this->plantime,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}