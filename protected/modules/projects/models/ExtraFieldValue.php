<?php

/**
 * Модель для работы со значениями дополнительных полей заявки
 *
 * Таблица '{{extra_field_values}}':
 * @property integer $id
 * @property string $instanceid
 * @property string $questionaryid
 * @property string $value
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property ExtraField         $fieldObject
 * @property ExtraFieldInstance $fieldInstance
 * @property Questionary        $questionary
 * 
 * @todo документировать код
 * @todo прописать unique-правило в rules
 */
class ExtraFieldValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{extra_field_values}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('instanceid, questionaryid, timecreated, timemodified', 'length', 'max' => 11),
			array('value', 'length', 'max' => 4095),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, instanceid, questionaryid, value, timecreated, timemodified', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	    );
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'fieldInstance' => array(self::BELONGS_TO, 'ExtraFieldInstance', 'instanceid'),
		    'fieldObject'   => array(self::BELONGS_TO, 'ExtraField', 'fieldid', 'through' => 'fieldInstance'),
		    'questionary'   => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        $criteria = new CDbCriteria();
	        $criteria->compare('instanceid', $this->instanceid);
	        $criteria->compare('questionaryid', $this->questionaryid);
	        if ( $this->exists($criteria) )
	        {// запрещаем прикреплять одно и то же значение поля к одному полю более одного раза 
	            // для одного пользователя
	            // + при извлечении данных мы точно знаем какое значение откуда
	            // - невозможно хранить списки значений в дополнительных полях (но пока и не требуется)
	           return false;
	        }
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * Получить все значения всех дополнительных полей для определенной анкеты 
	 * (именованая группа условий)
	 * 
	 * @param int $id
	 * @return ExtraFieldValue
	 */
	public function forQuestionary($questionary)
	{
	    if ( ! is_object($questionary) )
	    {
	        $questionary = Questionary::model()->findByPk($questionary);
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare('questionaryid', $questionary->id);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Получить значения для определенного поля от всех пользователей по всем объектам 
	 * (именованая группа условий)
	 * 
	 * @param int $id
	 * @return ExtraFieldValue
	 * 
	 * @todo принимать объект в качестве параметра
	 */
	public function forField($fieldId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'fieldInstance' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forField' => array($fieldId),
	            ),
	        ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Получить значения всех дополнительных полей от всех пользователей по одной роли 
	 * (именованая группа условий)
	 * 
	 * @param EventVacancy $vacancyId
	 * @return ExtraFieldValue
	 */
	public function forVacancy($vacancy)
	{
	    if ( ! is_object($vacancy) )
	    {
	        $vacancy = EventVacancy::model()->findByPk($vacancy);
	    }
	    if ( $vacancy->regtype === 'form' )
	    {
	        return $this->forObject('vacancy', $vacancy->id);
	    }else
	    {
	        $ids = $vacancy->getWizardStepInstanceIds();
	        return $this->forObjects('wizardstepinstance', $ids);
	    }
	}
	
	/**
	 * Получить значения всех дополнительных полей от всех пользователей по одному объекту
	 * (именованая группа условий)
	 *
	 * @param string $objectType
	 * @param int $objectId
	 * @return ExtraFieldValue
	 */
	public function forObject($objectType, $objectId)
	{
	    if ( ! $objectType OR ! $objectId )
	    {
	        throw new CException('Не передан обязательный параметр для условия поиска');
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'fieldInstance' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObject' => array($objectType, $objectId),
	            ),
	        ),
	    );
	    $criteria->together = true;
	    
	    /*$criteria->with = array('fieldInstance');
	    $criteria->together = true;
	    $criteria->compare('fieldInstance.objecttype', $objectType);
	    $criteria->compare('fieldInstance.objectid', $objectId);*/
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Получить значения всех дополнительных полей от всех пользователей по одному объекту
	 * (именованая группа условий)
	 *
	 * @param string $objectType
	 * @param array  $objectIds
	 * @return ExtraFieldValue
	 */
	public function forObjects($objectType, $objectIds)
	{
	    if ( ! $objectType OR ! $objectIds )
	    {
	        throw new CException('Не передан обязательный параметр для условия поиска');
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'fieldInstance' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObject' => array($objectType, $objectIds),
	            ),
	        ),
	    );
	    $criteria->together = true;
	    
	    /*$criteria->with = array('fieldInstance');
	    $criteria->together = true;
	    $criteria->compare('fieldInstance.objecttype', $objectType);
	    $criteria->addInCondition('fieldInstance.objectid', $objectIds);*/
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'instanceid' => 'Instanceid',
			'questionaryid' => 'Questionaryid',
			'value' => 'Value',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
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
		$criteria->compare('instanceid', $this->instanceid,true);
		$criteria->compare('questionaryid', $this->questionaryid,true);
		$criteria->compare('value', $this->value,true);
		$criteria->compare('timecreated', $this->timecreated,true);
		$criteria->compare('timemodified', $this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExtraFieldValue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
