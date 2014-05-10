<?php

/**
 * This is the model class for table "{{q_notification_settings}}".
 *
 * The followings are the available columns in table '{{q_notification_settings}}':
 * @property integer $id
 * @property string $questionaryid
 * @property string $source
 * @property string $type
 * @property integer $enabled
 * @property integer $email
 * @property integer $sms
 * @property string $minsalary
 * @property string $timecreated
 * @property string $timemodified
 */
class QNotificationSettings extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{q_notification_settings}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('source', 'required'),
			array('enabled, email, sms', 'numerical', 'integerOnly'=>true),
			array('questionaryid, minsalary, timecreated, timemodified', 'length', 'max'=>11),
			array('source, type', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, questionaryid, source, type, enabled, email, sms, minsalary, timecreated, timemodified', 'safe', 'on'=>'search'),
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'questionaryid' => 'Questionaryid',
			'source' => 'Source',
			'type' => 'Type',
			'enabled' => 'Enabled',
			'email' => 'Email',
			'sms' => 'Sms',
			'minsalary' => 'Minsalary',
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
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('enabled',$this->enabled);
		$criteria->compare('email',$this->email);
		$criteria->compare('sms',$this->sms);
		$criteria->compare('minsalary',$this->minsalary,true);
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
	 * @return QNotificationSettings the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
