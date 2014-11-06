<?php

/**
 * Модель для хранения настроек виджета одного поля формы
 *
 * Таблица '{{flexible_form_fields}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $widget
 * @property string $name
 * @property string $label
 * @property string $labeloptions
 * @property string $hint
 * @property string $hintoptions
 * @property string $prepend
 * @property string $prependoptions
 * @property string $append
 * @property string $appendoptions
 * @property integer $clientvalidation
 * @property integer $ajaxvalidation
 * @property string $htmloptions
 * @property string $timecreated
 * @property string $timemodified
 */
class FlexibleFormFields extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{flexible_form_fields}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, htmloptions', 'required'),
			array('clientvalidation, ajaxvalidation', 'numerical', 'integerOnly'=>true),
			array('objecttype', 'length', 'max'=>64),
			array('objectid, timecreated, timemodified', 'length', 'max'=>11),
			array('widget, name, label, labeloptions, hint, hintoptions, prepend, prependoptions, append, appendoptions, htmloptions', 'length', 'max'=>255),
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
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'widget' => 'Widget',
			'name' => 'Name',
			'label' => 'Label',
			'labeloptions' => 'Labeloptions',
			'hint' => 'Hint',
			'hintoptions' => 'Hintoptions',
			'prepend' => 'Prepend',
			'prependoptions' => 'Prependoptions',
			'append' => 'Append',
			'appendoptions' => 'Appendoptions',
			'clientvalidation' => 'Clientvalidation',
			'ajaxvalidation' => 'Ajaxvalidation',
			'htmloptions' => 'Htmloptions',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FlexibleFormFields the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
