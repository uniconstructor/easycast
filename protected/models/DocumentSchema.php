<?php

/**
 * Схема документа: хранит структуру документа, его набор полей
 * Одна схема используется для одного типа документа
 *
 * Таблица '{{document_schemas}}':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $formid
 * @property string $freebasetype
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property Document[] $documents
 */
class DocumentSchema extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{document_schemas}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('title, freebasetype', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('type', 'length', 'max' => 127),
			array('timecreated, timemodified, formid', 'length', 'max'=>11),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // все документы, структура которых описывается этой схемой
		    'documents' => array(self::HAS_MANY, 'Document', 'schemaid'),
		    // форма для создания модели по этой схеме
		    'flexibleForm' => array(self::HAS_ONE, 'FlexibleForm', 'formid'),
		    // @todo все поля документов хранящие в себе данные со структурой этой схемы
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
	        // это поведение позволяет изменять набор связей модели в процессе выборки
	        'CustomScopesBehavior' => array(
	            'class' => 'application.behaviors.CustomScopesBehavior',
	        ),
	        // это поведение позволяет изменять набор связей модели в зависимости от того какие данные в ней находятся
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
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
			'type' => 'Type',
			'formid' => 'formid',
			'freebasetype' => 'Freebasetype',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return DocumentSchema the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
