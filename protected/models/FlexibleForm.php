<?php

/**
 * Модель формы с произвольным набором полей
 * Используется для редактирования модели Document и для автоматического составления 
 * формы по полям модели
 *
 * Таблица '{{flexible_forms}}':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $action
 * @property string $activeformoptions
 * @property string $displaytype
 * @property integer $clientvalidation
 * @property integer $ajaxvalidation
 * @property string $timecreated
 * @property string $timemodified
 */
class FlexibleForm extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{flexible_forms}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('clientvalidation, ajaxvalidation', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>6),
			array('description, activeformoptions', 'length', 'max'=>4095),
			array('action', 'length', 'max'=>255),
			array('displaytype', 'length', 'max'=>12),
			array('timecreated, timemodified', 'length', 'max'=>11),
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
			'title' => 'Title',
			'description' => 'Description',
			'action' => 'Action',
			'activeformoptions' => 'Activeformoptions',
			'displaytype' => 'Displaytype',
			'clientvalidation' => 'Clientvalidation',
			'ajaxvalidation' => 'Ajaxvalidation',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		);
	}
    
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FlexibleForm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
