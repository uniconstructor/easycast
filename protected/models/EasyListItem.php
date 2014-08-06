<?php

/**
 * Элемент списка пользователей
 *
 * The followings are the available columns in table '{{user_list_items}}':
 * @property integer $id
 * @property string $easylistid
 * @property string $questionaryid
 * @property string $timecreated
 * @property string $timemodified
 * @property string $status
 */
class EasyListItem extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{easy_list_items}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('status', 'required'),
			array('easylistid, questionaryid, timecreated, timemodified', 'length', 'max'=>11),
			array('status', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, easylistid, questionaryid, timecreated, timemodified, status', 'safe', 'on'=>'search'),
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
			'easylistid' => 'Userlistid',
			'questionaryid' => 'Questionaryid',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'status' => 'Status',
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
		$criteria->compare('easylistid',$this->easylistid,true);
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserListItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
