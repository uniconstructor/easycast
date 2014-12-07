<?php

/**
 * Модель данных для хранения структуры формы с произвольным набором полей
 * Используется для редактирования модели Document и для 
 * автоматического составления формы по полям модели
 *
 * Таблица '{{flexible_forms}}':
 * @property integer $id
 * @property string  $title
 * @property string  $method
 * @property string  $description
 * @property string  $action
 * @property string  $activeformoptions
 * @property string  $displaytype
 * @property integer $clientvalidation
 * @property integer $ajaxvalidation
 * @property string  $timecreated
 * @property string  $timemodified
 * 
 * Relations:
 * @property DocumentSchema[]    $schemas
 * @property FlexibleFormField[] $fields
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
			array('clientvalidation, ajaxvalidation', 'numerical', 'integerOnly' => true),
			array('method', 'length', 'max' => 6),
			array('description, activeformoptions', 'length', 'max' => 4095),
			array('action, title', 'length', 'max' => 255),
			array('displaytype', 'length', 'max' => 12),
			array('timecreated, timemodified', 'length', 'max' => 11),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
		    // схемы использующие эту форму
            'schemas' => array(self::HAS_MANY, 'DocumentSchema', 'formid'),
		    // поля этой формы
		    'fields'  =>  array(self::HAS_MANY, 'FlexibleFormField', 'objectid', 
		        'condition' => array(
                    'scopes' => array(
                        'withObjectType' => array('FlexibleForm'),
                    ),
                ),
		    ),
		);
		// подключаем связи для настроек
		if ( ! $this->asa('ConfigurableRecordBehavior') )
		{
		    $this->attachBehavior('ConfigurableRecordBehavior', array(
		        'class' => 'application.behaviors.ConfigurableRecordBehavior',
		        'defaultOwnerClass' => get_class($this),
		    ));
		}
		$configRelations = $this->asa('ConfigurableRecordBehavior')->getDefaultConfigRelations();
		return CMap::mergeArray($relations, $configRelations);
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
	        'CustomRelationTargetBehavior' => array(
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
			'title' => 'Title',
			'description' => 'Description',
			'method' => 'Метод отправки (рекомендуется post)',
			'action' => 'Action',
			'activeformoptions' => 'Activeformoptions',
			'displaytype' => 'Displaytype',
			'clientvalidation' => 'Clientvalidation',
			'ajaxvalidation' => 'Ajaxvalidation',
			'timecreated' => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
		);
	}
    
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return FlexibleForm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
