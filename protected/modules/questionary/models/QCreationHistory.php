<?php

/**
 * История создания анкет администраторами
 * Таблица "{{q_creation_history}}".
 *
 * The followings are the available columns in table '{{q_creation_history}}':
 * @property integer $id
 * @property string $userid
 * @property string $questionaryid
 * @property string $timecreated
 */
class QCreationHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QCreationHistory the static model class
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
		return '{{q_creation_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('userid, questionaryid, timecreated', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userid, questionaryid, timecreated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    'user'        => array(self::BELONGS_TO, 'User', 'userid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'            => 'ID',
			'userid'        => 'Userid',
			'questionaryid' => 'Questionaryid',
			'timecreated'   => 'Timecreated',
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
		$criteria->compare('userid',$this->userid,true);
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('timecreated',$this->timecreated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}