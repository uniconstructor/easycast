<?php

/**
 * This is the model class for table "{{project_managers}}".
 *
 * The followings are the available columns in table '{{project_managers}}':
 * @property integer $id
 * @property string $managerid
 * @property string $projectid
 * @property string $timestart
 * @property string $timeend
 * @property string $status
 */
class ProjectManager extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectManager the static model class
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
		return '{{project_managers}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('managerid, projectid, timestart, timeend', 'length', 'max' => 11),
			array('status', 'length', 'max' => 9),
			// The following rule is used by search().
			array('id, managerid, projectid, timestart, timeend, status', 'safe', 'on' => 'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'managerid' => 'Managerid',
			'projectid' => 'Projectid',
			'timestart' => 'Timestart',
			'timeend' => 'Timeend',
			'status' => 'Status',
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
		$criteria->compare('managerid',$this->managerid,true);
		$criteria->compare('projectid',$this->projectid,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}