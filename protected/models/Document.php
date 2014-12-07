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
 * @property string $workflowid
 * @property string $status
 * 
 * Relations:
 * @property DocumentSchema        $schema
 * @property DocumentData[]        $dataItems
 * @property DocumentDataHistory[] $historyItems
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
			array('schemaid, timecreated, timemodified', 'length', 'max' => 11),
			array('status, workflowid', 'length', 'max' => 50),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
		    // схема документа: хранит структуру полей документа
		    'schema' => array(self::BELONGS_TO, 'DocumentSchema', 'schemaid'),
		    // данные модели документа
		    'dataItems' => array(self::HAS_MANY, 'DocumentData', 'documentid'),
		    // история изменений документа
		    'historyItems' => array(self::HAS_MANY, 'DocumentDataHistory', 'documentid'),
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
			'id'           => 'ID',
			'schemaid'     => 'Схема документа',
			'timecreated'  => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
			'status'       => Yii::t('coreMessages', 'status'),
			'workflowid'   => 'Рабочий процесс',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param string $className active record class name.
	 * @return Document the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
