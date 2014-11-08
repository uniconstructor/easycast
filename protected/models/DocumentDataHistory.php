<?php

/**
 * История изменеий документа: в эту таблицу сохраняется старое значение 
 * при каждом обновлении DocumentData
 *
 * Таблица '{{document_data_history}}':
 * @property integer $id
 * @property string $documentid
 * @property string $schemafieldid
 * @property string $value
 * @property string $freebaseitem
 * @property string $timecreated
 * @property string $version
 * @property string $userid
 * @property string $comment
 * 
 * Relations:
 * @property Document       $document
 * @property DocumentSchema $schema
 * @property ExtraField     $fieldObject
 */
class DocumentDataHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{document_data_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('documentid, schemafieldid, timecreated, version, userid', 'length', 'max' => 11),
			array('value', 'length', 'max' => 4095),
			array('freebaseitem', 'length', 'max' => 255),
			array('comment', 'length', 'max' => 127),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // документ для которого храним данные
		    'document' => array(self::BELONGS_TO, 'Document', 'documentid'),
		    // схема поля документа: (если поле документа тоже хранит в себе документ)
		    'schema' => array(self::BELONGS_TO, 'DocumentSchema', 'schemaid'),
		    // схема поля документа: (если поле документа тоже хранит в себе документ)
		    'fieldObject' => array(self::BELONGS_TO, 'ExtraField', 'extrafieldid'),
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
	            'timemodified' => null,
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
			'schemafieldid' => 'Schemafieldid',
			'value' => 'Value',
			'freebaseitem' => 'Freebaseitem',
			'timecreated'  => Yii::t('coreMessages', 'timecreated'),
			'version' => 'Version',
			'userid' => 'Кем изменено',
			'comment' => Yii::t('coreMessages', 'comment'),
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return DocumentDataHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
