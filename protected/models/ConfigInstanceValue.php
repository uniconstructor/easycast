<?php

/**
 * This is the model class for table "{{config_instance_values}}".
 *
 * The followings are the available columns in table '{{config_instance_values}}':
 * @property integer $id
 * @property string $configinstanceid
 * @property string $type
 * @property string $value
 * @property string $timecreated
 * @property string $timemodified
 */
class ConfigInstanceValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{config_instance_values}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('configinstanceid, timecreated, timemodified', 'length', 'max'=>11),
			array('type', 'length', 'max'=>20),
			array('value', 'length', 'max'=>4095),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, configinstanceid, type, value, timecreated, timemodified', 'safe', 'on'=>'search'),
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
			'configinstanceid' => 'Configinstanceid',
			'type' => 'Type',
			'value' => 'Value',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
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
		$criteria->compare('configinstanceid',$this->configinstanceid,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConfigInstanceValue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
