<?php

/**
 * This is the model class for table "{{q_field_instances}}".
 *
 * The followings are the available columns in table '{{q_field_instances}}':
 * @property integer $id
 * @property string $fieldid
 * @property string $objecttype
 * @property string $objectid
 * @property string $filling
 * @property string $condition
 * @property string $data
 * @property string $timecreated
 * @property string $timemodified
 * @property string $sortorder
 * 
 * Relations:
 * @property QUserField $fieldObject
 * 
 * @deprecated заменить схемой документа
 */
class QFieldInstance extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.questionary.QuestionaryModule');
    }
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{q_field_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype', 'required'),
			array('fieldid, objectid, timecreated, timemodified, sortorder', 'length', 'max' => 11),
			array('objecttype, filling, condition', 'length', 'max' => 50),
			array('newlabel', 'length', 'max' => 255),
			array('newdescription', 'length', 'max' => 2047),
			array('data', 'length', 'max' => 1023),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fieldid, objecttype, objectid, filling, condition, data, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// автоматически назначаем порядок сортировки для новых записей, добавляя их в конец списка
    	    // @todo вынести в behavior
    	    $lastItemCount = $this->forObject($this->objecttype, $this->objectid)->count();
    	    $lastItemCount++;
    	    $this->sortorder = $lastItemCount;
	    }
	    return parent::beforeSave();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'fieldObject' => array(self::BELONGS_TO, 'QUserField', 'fieldid'),
		    // роль, к которой привязаны поля
		    //'vacancy' => array(self::BELONGS_TO, 'EventVacancy', 'objectid'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'fieldid' => 'Поле анкеты',
			'objecttype' => 'Objecttype',
			'objectid' => 'Шаг регистрации',
			'filling' => 'Обязательно к заполнению?',
			'condition' => 'Condition',
			'data' => 'Изначальное значение',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'sortorder' => 'Сортировка',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('fieldid', $this->fieldid, true);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('filling', $this->filling, true);
		$criteria->compare('condition', $this->condition, true);
		$criteria->compare('data', $this->data, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('timemodified', $this->timemodified, true);
		$criteria->compare('sortorder', $this->sortorder, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return QFieldInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => $this->getTableAlias(true, false).'.`sortorder` ASC'
	    );
	}
    
	/**
	 * @see CActiveRecord::scopes()
	 */
    public function scopes()
    {
        return array(
            // последние поданные заявки
            'lastCreated' => array(
                'order' => $this->getTableAlias(true).'.`timecreated` DESC'
            ),
            // последние измененные заявки
            'lastModified' => array(
                'order' => $this->getTableAlias(true).'.`timemodified` DESC'
            ),
            // последние поданные заявки
            'firstCreated' => array(
                'order' => $this->getTableAlias(true).'.`timecreated` ASC'
            ),
        );
    } 
	
	/**
	 * Получить название фильтра поиска
	 * @return string
	 */
	public function getName()
	{
	    if ( $this->isNewRecord )
	    {
	        return;
	    }
	    return $this->fieldObject->label;
	}
	
	/**
	 * Получить описание фильтра поиска
	 * @return string
	 */
	public function getDescription()
	{
	    if ( $this->isNewRecord )
	    {
	        return;
	    }
	    return $this->fieldObject->description;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getFillingMode()
	{
	    if ( $this->isNewRecord )
	    {
	        return;
	    }
	    $modes = $this->getFillingModes();
	    return $modes[$this->filling];
	}
	
	/**
	 * Получить список возможных вариантов заполнения поля
	 * 
	 * @return array
	 */
	public function getFillingModes()
	{
	    return array(
	        'required'    => Yii::t('yii', 'Yes'),
	        'recommended' => Yii::t('yii', 'No'),
	        'forced'      => 'Автоматически задано',
	    );
	}
	
	/**
	 * 
	 * @return string
	 * 
	 * @deprecated
	 */
	public function getStepName()
	{
	    $stepInstance = $this->getLinkedStepInstance();
	    if ( $stepInstance )
	    {
	        return $stepInstance->step->name;
	    }
	    return '[]';
	}
	
	/**
	 * 
	 * @param int $id
	 * @return WizardStepInstance
	 * 
	 * @deprecated
	 */
	public function getLinkedStepInstance()
	{
	    if ( $this->objecttype === 'wizardstepinstance' )
	    {
	        $stepInstance = WizardStepInstance::model()->findByPk($this->objectid);
	    }
	    if ( $this->objecttype === 'vacancy' )
	    {
	        $criteria = new CDbCriteria();
	        $criteria->compare('objecttype', 'wizardstepinstance');
	        $criteria->compare('fieldid', $this->fieldid);
	        if ( ! $linkToStep = $this->find($criteria) )
	        {
	            return '';
	        }
	        $stepInstance = WizardStepInstance::model()->findByPk($this->objectid);
	    }
	    return $stepInstance;
	}
	
	/**
	 * Получить экземпляр записи привязаный к определенному полю 
	 * 
	 * @param  int $fieldId
	 * @return QFieldInstance
	 */
	public function forField($fieldId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`fieldid`', $fieldId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи, привязанные к определенному 
	 * объекту (например к роли)
	 * 
	 * @param  string $objectType - тип объекта к которому привязано поле
	 * @param  int $objectId - id объекта к которому привязано поле
	 * @return QFieldInstance
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи, привязанные к нескольким объектам одного типа
	 * 
	 * @param  string $objectType - тип объекта к которому привязано поле
	 * @param  array $objectIds - id объектов к которому привязано поле
	 * @return QFieldInstance
	 * 
	 * @deprecated
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->addInCondition('objectid', $objectIds);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи, привязанные 
	 * к определенному объекту (например к роли)
	 * @param string $objectType - тип объекта к которому привязано поле
	 * @param int $objectId - id объекта к которому привязано поле
	 * @return QFieldInstance
	 * 
	 * @deprecated оставлено для совместимости: везде используем forObject
	 */
	public function attachedTo($objectType, $objectId)
	{
	    return $this->forObject($objectType, $objectId);
	}
	
	/**
	 * 
	 * @param EventVacancy $vacancy
	 * @return QFieldInstance
	 */
	public function forVacancy($vacancy)
	{
		if ( $vacancy->regtype == 'form' )
	    {
	        return $this->forObject('vacancy', $vacancy->id);
	    }else
	    {
	        $ids = $vacancy->getWizardStepInstanceIds();
	        return $this->forObjects('wizardstepinstance', $ids);
	    }
	}
}