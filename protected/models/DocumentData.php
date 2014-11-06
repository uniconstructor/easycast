<?php

/**
 * Данные одного поля документа
 *
 * Таблица '{{document_data}}':
 * @property integer $id
 * @property string $documentid
 * @property string $schemafieldid
 * @property string $value
 * @property string $freebaseitem
 * @property string $timecreated
 * @property string $timemodified
 */
class DocumentData extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{document_data}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('documentid, schemafieldid, timecreated, timemodified', 'length', 'max'=>11),
			array('value', 'length', 'max'=>4095),
			array('freebaseitem', 'length', 'max'=>255),
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
			'documentid' => 'Documentid',
			'schemafieldid' => 'Schemafieldid',
			'value' => 'Value',
			'freebaseitem' => 'Freebaseitem',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DocumentData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
