<?php

/**
 * Данные одного поля документа
 *
 * Таблица '{{document_data}}':
 * @property integer $id
 * @property string $documentid
 * @property string $extrafieldid
 * @property string $value
 * @property string $freebaseitem
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property Document       $document   - документ для которого храним данные
 * @property DocumentSchema $schema     - схема поля документа: (если поле документа тоже хранит в себе документ)
 * @property ExtraField     $extraField - поле документа которому принадлежит это значение
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
			array('documentid, extrafieldid, timecreated, timemodified', 'length', 'max' => 11),
			array('value', 'length', 'max' => 4095),
			array('freebaseitem', 'length', 'max' => 255),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // документ которому принадлежит это значение
		    'document'   => array(self::BELONGS_TO, 'Document', 'documentid'),
		    // схема поля документа: (если поле документа тоже хранит в себе документ)
		    'schema'     => array(self::BELONGS_TO, 'DocumentSchema', 'schemaid'),
		    // поле документа которому принадлежит это значение
		    'extraField' => array(self::BELONGS_TO, 'ExtraField', 'extrafieldid'),
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
			'documentid' => 'Documentid',
			'extrafieldid' => 'Поле объекта',
			'value' => 'Value',
			'freebaseitem' => 'Freebaseitem',
			'timecreated'  => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return DocumentData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
