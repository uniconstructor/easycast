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
 * @property string $sortorder
 * 
 * Relations:
 * @property FlexibleForm $flexibleForm
 * @property ExtraField   $extraFields
 */
class FlexibleFormField extends CActiveRecord
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
			array('clientvalidation, ajaxvalidation', 'numerical', 'integerOnly' => true),
			array('objecttype', 'length', 'max' => 64),
			array('objectid, timecreated, timemodified, sortorder', 'length', 'max' => 11),
			array('widget, name, label, labeloptions, hint, hintoptions, prepend, 
			    prependoptions, append, appendoptions, htmloptions', 'length', 'max' => 255),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // форма которой принадлежит это поле
		    'flexibleForm' =>  array(self::BELONGS_TO, 'FlexibleForm', 'objectid'),
		    // поля, использующие эту форму
		    'extraFields'  => array(self::BELONGS_TO, 'ExtraField', 'formfieldid'),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'EcTimestampBehavior' => array(
	            'class' => 'application.behaviors.EcTimestampBehavior',
	        ),
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationSourceBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
	            'customRelations' => array(),
	        ),
	        // настройки для модели и методы для поиска по этим настройкам
	        'ConfigurableRecordBehavior' => array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
	        ),
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
			'sortorder' => 'sortorder',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return FlexibleFormField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
