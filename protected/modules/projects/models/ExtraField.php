<?php

/**
 * Модель для работы с дополнительными полями анкеты участника, которые возможно потребуются 
 * только один раз и только для заявки на конкретную роль 
 *
 * Таблица '{{extra_fields}}':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $title
 * @property string $description
 * @property string $timecreated
 * @property string $timemodified
 * @property string $valueschemaid
 * @property string $rules
 * @property string $freebaseproperty
 * @property string $optionslistid
 * @property string $parentid
 * @property string $formfieldid
 * 
 * Relations:
 * @property ExtraFieldInstance[]  $instances
 * @property ExtraField            $patent
 * @property EasyList              $optionsList
 * @property DocumentData[]        $dataItems
 * @property DocumentDataHistory[] $historyItems
 * @property FlexibleFormField     $formField
 * 
 * @todo документировать код
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
			array('name, type, title, description', 'filter', 'filter' => 'trim'),
			array('name, type, title', 'length', 'max' => 255),
			array('description, rulesб freebaseproperty', 'length', 'max' => 4095),
			array('timecreated, timemodified, valueschemaid, optionslistid, 
			    parentid, formfieldid', 'length', 'max' => 11),
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
	            'class' => 'zii.behaviors.EcTimestampBehavior',
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
		    /** @deprecated */
            'instances' => array(self::HAS_MANY, 'ExtraFieldInstance', 'fieldid'),
		    // шаблон-родитель для этого поля
		    'parent' => array(self::BELONGS_TO, 'ExtraField', 'patentid'),
		    // список возможных значений
		    'optionsList' => array(self::BELONGS_TO, 'EasyList', 'optionslistid'),
		    // поле формы для редактирования этого поля документа
		    'formField' => array(self::HAS_ONE, 'FlexibleFormField', 'formfieldid'),
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
	 * @see CActiveRecord::beforeDelete()
	 */
	public function beforeDelete()
	{
	    foreach ( $this->instances as $instance )
	    {
	        if ( ! $instance->delete() )
	        {
	            throw new CException('Не удалось удалить все экземпляры удаляемого поля');
	        }
	    }
	    return parent::beforeDelete();
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
			'title' => 'Название поля',
			'description' => 'Дополнительное пояснение',
			'timecreated' => 'Создание',
			'timemodified' => 'Последнее изменение',
			'valueschemaid' => 'Схема значения',
		    'rules' => 'Правила для проверки поля',
			'freebaseproperty' => 'Путь к описанию объекта на freebase',
			'optionslistid' => 'Список содержащий возможные значения поля (для полей с выбором варианта)',
			'parentid' => 'id поля-шаблона из значений которого было создано это поле',
			'formfieldid' => 'Поле формы отвечающее за ввод значения',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param  string $className active record class name.
	 * @return ExtraField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return array(
	        'orderByLabel' => array(
	            'order' => $this->getTableAlias(true).".`title` ASC",
	       ),
	    );
	}
	
	/**
	 * Именованая группа условий: получить все поля, привязаные к определенному объекту
	 * 
	 * @param string $objectType
	 * @param int $objectId
	 * @return ExtraField
	 * 
	 * @deprecated использовать CustomRelationSourceBehavior
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
	 * Именованая группа условий: получить все поля, привязаные к определенному объекту
	 * 
	 * @param string $objectType
	 * @param array $objectIds
	 * @return ExtraField
	 * 
	 * @deprecated использовать CustomRelationSourceBehavior
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
                    'forObjects' => array($objectType, $objectIds),
                ),
            ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - получить записи принадлежащие определенной роли
	 * 
	 * @param  EventVacancy $vacancy
	 * @return ExtraField
	 */
	public function forVacancy($vacancy)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forVacancy' => array($vacancy),
	            ),
	        ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - получить записи принадлежащие определенной роли
	 * 
	 * @param  Questionary $questionary
	 * @return ExtraField
	 * 
	 * @todo решить нужен ли этот метод здесь
	 */
	public function forQuestionary($questionary)
	{
	    throw new CException('NOT IMPLEMENTED');
	    /*$criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forQuestionary' => array($questionary),
	            ),
	        ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;*/
	}
	
	/**
	 * Именованая группа условий: получить все поля, привязаные к указанным категориям
	 * 
	 * @param  array $objectId
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
	                'forObjects' => array('category', $categoryIds),
                ),
            ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все поля, привязаные к указанным этапам регистрации
	 * 
	 * @param array $ids
	 * @return ExtraField
	 * 
	 * @deprecated больше не используется - шаги регистрации теперь хранятся в списках
	 */
	public function forStepInstances($ids)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObjects' => array('wizardstepinstance', $ids),
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
	    if ( ! $instance = $this->isAttachedTo($objectType, $objectId) )
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
	    $instance = ExtraFieldInstance::model()->forField($this->id)->
	       attachedTo($objectType, $objectId)->find();
        if ( ! $instance )
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
	 * 
	 * @param  EventVacancy $vacancy
	 * @return ExtraField
	 */
	public function isRequiredForVacancy($vacancy)
	{
	    $instance = ExtraFieldInstance::model()->forVacancy($vacancy)->find();
	    if ( ! $instance )
	    {// дополнительное поле вообще не прикреплено к этому объекту - 
            // значит оно не может быть обязательным
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
	 * 
	 * @param EventVacancy $vacancy - роль, на которую подается заявка
	 * @param Questionary $questionary - анкета, от имени которой подается заявка
	 * @return bool
	 */
	public function isEmptyForVacancy($vacancy, $questionary)
	{
	    if ( ! $this->isAttachedToVacancy($vacancy) )
	    {// поле вообще не прикреплено - в этом случае считаем что пользователь нам ничего не должен
	       return false;
	    }
	    if ( $questionary->isNewRecord )
	    {// анкета еще не сохранена
	       return true;
	    }
	    
	    // получаем объект содержащий значение поля
	    if ( ! $value = $this->getValueForVacancy($vacancy, $questionary->id, true) )
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
	 * 
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
	    
	    $value = ExtraFieldValue::model()->forField($this->id)->
	       forObject($objectType, $objectId)->forQuestionary($questionaryId)->find();
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
	 * Получить значение дополнительного поля привязанное к анкете + объекту
	 * 
	 * @param EventVacancy $vacancy
	 * @param int $questionaryId
	 * @param bool $asObject
	 * @return null|string|ExtraFieldValue
	 */
	public function getValueForVacancy($vacancy, $questionaryId, $asObject=false)
	{
	    if ( ! $this->isAttachedToVacancy($vacancy) )
	    {
	        return null;
	    }
	    if ( $vacancy->regtype === 'form' )
	    {
	        $value = ExtraFieldValue::model()->forField($this->id)->
	           forObject('vacancy', $vacancy->id)->forQuestionary($questionaryId)->find();
	    }else
	    {
	        $ids = $vacancy->getWizardStepInstanceIds();
	        $value = ExtraFieldValue::model()->forField($this->id)->
                forObjects('wizardstepinstance', $ids)->forQuestionary($questionaryId)->find();
	    }
	    
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
	 * 
	 * @param  string $objectType
	 * @param  int $objectId
	 * @return bool
	 */
	public function isAttachedTo($objectType, $objectId)
	{
	    return ExtraFieldInstance::model()->forField($this->id)->
            attachedTo($objectType, $objectId)->exists();
	}
	
	/**
	 * 
	 * @param  EvantVacancy $vacancy
	 * @return bool
	 */
	public function isAttachedToVacancy($vacancy)
	{
	    return ExtraFieldInstance::model()->forField($this->id)->forVacancy($vacancy)->exists();
	}
	
	/**
	 * Определить, является ли поле "потерянным" - то есть не принадлежащим ни одной категории
	 * 
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
	 * 
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
	 * 
	 * @return string
	 * 
	 * @todo перенести в список стандартных значений
	 */
	public function getTypeOption()
	{
	    $types = $this->getTypeOptions();
	    return $types[$this->type];
	}
	
	/**
	 * @return string
	 *
	 * @todo оставлено после переименования поля (для работы старых функций), удалить при рефакторинге
	 */
	public function getLabel()
	{
	    return $this->title;
	}
	/**
	 * @return void
	 *
	 * @todo оставлено после переименования поля (для работы старых функций), удалить при рефакторинге
	 */
	public function setLabel($title)
	{
	    $this->title = $title;
	}
}
