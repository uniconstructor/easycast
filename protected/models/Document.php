<?php

/**
 * Модель "документ" для хранения записей любой структуры, с возможностью добавлять
 * любое количество любых дополнительных полей к любому объекту
 *
 * Таблица '{{documents}}':
 * @property integer $id
 * @property string $schemaid
 * @property string $timecreated
 * @property string $timemodified
 * @property string $status
 * 
 * Relations:
 * 
 */
class Document extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{documents}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('schemaid, timecreated, timemodified', 'length', 'max'=>11),
			array('status', 'length', 'max'=>50),
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
			'id'           => 'ID',
			'schemaid'     => 'Schemaid',
			'timecreated'  => 'Timecreated',
			'timemodified' => 'Timemodified',
			'status'       => 'Status',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Document the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
