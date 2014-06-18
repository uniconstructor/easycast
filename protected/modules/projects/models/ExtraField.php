<?php

/**
 * This is the model class for table "{{extra_fields}}".
 *
 * The followings are the available columns in table '{{extra_fields}}':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $label
 * @property string $description
 * @property string $timecreated
 * @property string $timemodified
 * 
 * @todo документировать код
 * @todo прописать MANY_MANY relation с ролями 
 */
class ExtraField extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{extra_fields}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
		    // в названии поля могут быть только латинские буквы, цифры и подчеркивание
		    array('name', 'match', 'pattern' => '/^([a-z0-9_])+$/'),
		    // название поля должно быть уникальным во всем списке доп. полей
		    array('name', 'unique', 
                'attributeName' => 'name',
		        'className'     => 'ExtraField',
		        'on'            => 'insert',
            ),
		    array('name', 'unique', 
                'attributeName' => 'name',
		        'className'     => 'ExtraField',
		        'criteria'      => array('condition' => "`id` <> '$this->id'"),
		        'on'            => 'update',
            ),
			array('name, type, label, description', 'filter', 'filter' => 'trim'),
			array('name, type, label', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('timecreated, timemodified', 'length', 'max' => 11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, type, label, description, timecreated, timemodified', 'safe', 'on' => 'search'),
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
		    // все ссылки на это поле
            'instances' => array(self::HAS_MANY, 'ExtraFieldInstance', 'fieldid'),
		    // все роли, к которым прикреплено это поле
		    // @todo проверить правильно ли указан порядок полей в составном ключе
		    /*'vacancies' => array(self::MANY_MANY, 'EventVacancy', "{{extra_field_instances}}(fieldid, objectid)",
		        'condition' => "`objecttype` = 'vacancy'",
		    ),*/
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    return parent::beforeSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Служебное название',
			'type' => 'Тип поля',
			'label' => 'Название (как этот текст)',
			'description' => 'Дополнительное пояснение',
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
		$criteria->compare('name', $this->name, true);
		$criteria->compare('type', $this->type, true);
		$criteria->compare('label', $this->label, true);
		$criteria->compare('description', $this->description, true);
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
	 * @return ExtraField the static model class
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
    	    'order' => "`label` ASC",
    	);
	}
	
	/**
	 * Именованая группа условий: получить все поля, привязаные к определенному объекту
	 * @param string $objectType
	 * @param int $objectId
	 * @return ExtraField
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
                    'forObject' => array($objectType, $objectId),
                ),
            ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все поля, привязаные к указанным категориям
	 * @param array $objectId
	 * @return ExtraField
	 */
	public function forCategories($categoryIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
                    'forType'    => array('category'),
	                'forObjects' => array($categoryIds),
                ),
            ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Определить, пусто ли требующее заполнения дополнительное поле
	 * 
	 * @param string $objectType - 
	 * @param int    $objectId - 
	 * @param int    $questionaryId - id анкеты, для которой проверяется заполненность
	 * @return bool
	 */
	public function isEmptyFor($objectType, $objectId, $questionaryId)
	{
	    if ( ! $instance = $this->isAttachedTo($objectType, $vacancy->id) )
	    {// поле вообще не прикреплено - в этом случае считаем что пользователь нам ничего не должен 
	        return false;
	    }
	    // получаем объект 
	    $value = ExtraFieldValue::model()->forField($this->id)->forObject($objectType, $objectId)->
	       forQuestionary($questionaryId)->find();
	    if ( ! $value )
	    {// запись со значением еще не создана
	        return true;
	    }
	    if ( ! $value->value )
	    {// запись создана, но значение не заполнено
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, пусто ли требующее заполнения дополнительное поле
	 *
	 * @param  string $objectType -
	 * @param  int    $objectId -
	 * @return bool
	 *
	 * @todo документировать
	 */
	public function isRequiredFor($objectType, $objectId)
	{
        if ( ! $instance = ExtraFieldInstance::model()->forField($this->id)->attachedTo($objectType, $objectId)->find() )
        {// дополнительное поле вообще не прикреплено к этому объекту - значит оно не может быть обязательным
            return false;
        }
        if ( $instance->filling === 'required' )
        {
            return true;
        }
        return false;
	}
	
	/**
	 * Определить, пусто ли требующее заполнения дополнительное поле (для подачи заявки)
	 * @param EventVacancy $vacancy - роль, на которую подается заявка
	 * @param Questionary $questionary - анкета, от имени которой подается заявка
	 * @return bool
	 */
	public function isEmptyForVacancy($vacancy, $questionary)
	{
	    if ( ! $instance = $this->isAttachedTo('vacancy', $vacancy->id) )
	    {// поле вообще не прикреплено - в этом случае считаем что пользователь нам ничего не должен 
	        return false;
	    }
	    if ( $questionary->isNewRecord )
	    {// анкета еще не сохранена
	        return true;
	    }
	    // получаем объект содержащий значение поля
	    if ( ! $value = $this->getValueFor('vacancy', $vacancy->id, $questionary->id, true) )
	    {// запись со значением еще не создана - поле не заполнено
	        return true;
	    }
	    if ( ! $value->value )
	    {// запись создана, но значение не заполнено
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Получить значение дополнительного поля привязанное к анкете + объекту
	 * @param string $objectType
	 * @param int $objectId
	 * @param int $questionaryId
	 * @param bool $asObject
	 * @return null|string|ExtraFieldValue
	 */
	public function getValueFor($objectType, $objectId, $questionaryId, $asObject=false)
	{
	    if ( ! $this->isAttachedTo($objectType, $objectId) )
	    {
	        return null;
	    }
	    $value = ExtraFieldValue::model()->forField($this->id)->forObject($objectType, $objectId)->
	       forQuestionary($questionaryId)->find();
	    if ( ! $value )
	    {
	        return null;
	    }
	    if ( $asObject )
	    {
	        return $value;
	    }
	    return $value->value;
	}
	
	/**
	 * Определить, прикреплено ли дополнительное поле к объекту
	 * @param string $objectType
	 * @param int $objectId
	 * @return bool
	 */
	public function isAttachedTo($objectType, $objectId)
	{
	    return ExtraFieldInstance::model()->forField($this->id)->attachedTo($objectType, $objectId)->exists();
	}
	
	/**
	 * Определить, является ли поле "потерянным" - то есть не принадлежащим ни одной категории
	 * @return bool
	 *         true - да, поле беспризорное :)
	 *         false - поле находится хотя бы в одной категории
	 */
	public function isOrphaned()
	{
	    return ! ExtraFieldInstance::model()->forField($this->id)->forType('category')->exists();
	}
	
	/**
	 * Получить список возможных вариантов содержимого для категории
	 * @return array
	 *
	 * @todo перенести в список стандартных значений
	 */
	public function getTypeOptions()
	{
	    return array(
	        'textarea' => 'Текстовое поле [textarea]',
	        'text'     => 'Текстовая строка [text]',
	        'checkbox' => 'Галочка [checkbox]',
	    );
	}
	
	/**
	 * Получить текущее значение для пользователя
	 * @return string
	 * 
	 * @todo перенести в список стандартных значений
	 */
	public function getTypeOption()
	{
	    $types = $this->getTypeOptions();
	    return $types[$this->type];
	}
}
