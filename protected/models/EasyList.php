<?php

/**
 * Списки пользователей "{{user_lists}}".
 *
 * The followings are the available columns in table '{{user_lists}}':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $allowupdate
 * @property string $updatemethod
 * @property string $timecreated
 * @property string $timemodified
 * @property string $timeupdated
 * @property string $updateperiod
 */
class EasyList extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{easy_lists}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('allowupdate', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('description', 'length', 'max'=>4095),
			array('updatemethod', 'length', 'max'=>10),
			array('timecreated, timemodified, timeupdated, updateperiod', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, allowupdate, updatemethod, timecreated, timemodified, timeupdated, updateperiod', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'description' => 'Description',
			'allowupdate' => 'Allowupdate',
			'updatemethod' => 'Updatemethod',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'timeupdated' => 'Timeupdated',
			'updateperiod' => 'Updateperiod',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('allowupdate',$this->allowupdate);
		$criteria->compare('updatemethod',$this->updatemethod,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('timeupdated',$this->timeupdated,true);
		$criteria->compare('updateperiod',$this->updateperiod,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
