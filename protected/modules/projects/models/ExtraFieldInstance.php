<?php

/**
 * This is the model class for table "{{extra_field_instances}}".
 *
 * The followings are the available columns in table '{{extra_field_instances}}':
 * @property integer $id
 * @property string $fieldid
 * @property string $objecttype
 * @property string $objectid
 * @property string $filling
 * @property string $condition
 * @property string $data
 * @property string $timecreated
 * @property string $timemodified
 * @property string $default
 * 
 * Relations:
 * @property ExtraField $fieldObject
 * @property ExtraFieldValue[] $instanceValues - введенные значения пользователей
 * 
 * @todo документировать код
 * @todo прописать unique-правило в rules
 */
class ExtraFieldInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{extra_field_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype, filling', 'required'),
			array('objecttype, filling, condition', 'length', 'max' => 50),
			array('objecttype, filling, condition', 'filter', 'filter' => 'trim'),
		    
			array('fieldid, objectid, timecreated, timemodified', 'length', 'max' => 11),
		    
			array('default', 'length', 'max' => 255),
			array('data', 'length', 'max' => 1023),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fieldid, objecttype, objectid, filling, condition, data, timecreated, timemodified', 
			    'safe', 'on' => 'search'),
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
		    // поле, которое прикреплено к объекту
		    'fieldObject' => array(self::BELONGS_TO, 'ExtraField', 'fieldid'),
		    // роль, к которой привязаны поля
		    'vacancy' => array(self::BELONGS_TO, 'EventVacancy', 'objectid'),
		    // все значения, добавленные пользователями для этого поля
		    'instanceValues' => array(self::HAS_MANY, 'ExtraFieldValue', 'instanceid'),
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
	        $criteria->compare('objecttype', $this->objecttype);
	        $criteria->compare('objectid', $this->objectid);
	        $criteria->compare('fieldid', $this->fieldid);
	        if ( $this->exists($criteria) )
	        {// запрещаем прикреплять одно и то же поле к одному объекту более одного раза
	            return false;
	        }
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
	    if ( $this->isNewRecord )
	    {
	        if ( $this->objecttype === 'vacancy' AND 
	             ! ( $this->default === '' OR $this->default === null ) AND
	             $members = ProjectMember::model()->forVacancy($this->objectid)->findAll() )
	        {// если новое поле прикрепляется к роли - установим всем ранее подавшим заявку
	            // участникам значения по умолчанию
	            foreach ( $members as $member )
	            {
	                $value = new ExtraFieldValue();
	                $value->instanceid    = $this->id;
	                $value->questionaryid = $member->questionary->id;
	                $value->value         = $this->default;
	                $value->save();
	            }
	        }
	    }
	    parent::afterSave();
	}
	
	/**
	 * Именованая группа условий: получить все записи о доп. полях, привязанных к определенному объекту
	 * (например к роли)
	 * @param string $objectType - тип объекта к которому привязано поле
	 * @param int $objectId - id объекта к которому привязано поле
	 * @return ExtraFieldInstance
	 */
	public function attachedTo($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->compare('objectid', $objectId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - получить записи принадлежащие определенной роли
	 * @param int $vacancyId - id роли, на которую подана заявка
	 * @return ExtraFieldInstance
	 */
	public function forVacancy($vacancyId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', 'vacancy');
	    $criteria->compare('objectid', $vacancyId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - получить записи (ссылки) на доп. поле,
	 * или другими словами - посмотреть к каким объектам оно когда-либо прикреплялось
	 * @param int $fieldId - id дополнительного поля
	 * @return ExtraFieldInstance
	 */
	public function forField($fieldId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('fieldid', $fieldId);
	     
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
			'fieldid' => 'Дополнительное поле',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'filling' => 'Обязательно к заполнению?',
			'condition' => 'Condition',
			'data' => 'Изначальное значение',
			'default' => 'Чем заполнить ранее поданые заявки?',
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
		$criteria->compare('fieldid', $this->fieldid, true);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('filling', $this->filling, true);
		$criteria->compare('condition', $this->condition, true);
		$criteria->compare('data', $this->data, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('timemodified', $this->timemodified, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExtraFieldInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
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
	    if ( $this->filling === 'required' )
	    {
	        return 'Да';
	    }elseif ( $this->filling === 'recommended' )
	    {
	        return 'Нет';
	    }
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getFillingModes()
	{
	    return array(
	        'required'    => 'Да',
	        'recommended' => 'Нет',
	    );
	}
}
