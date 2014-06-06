<?php

/**
 * This is the model class for table "{{q_user_fields}}".
 *
 * The followings are the available columns in table '{{q_user_fields}}':
 * @property integer $id
 * @property string $name
 * @property string $storage
 * @property string $fillpoints
 * @property string $external
 * @property string $multiple
 * @property string $type
 */
class QUserField extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.questionary.models.Questionary');
        parent::init();
    }
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{q_user_fields}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, external, multiple', 'required'),
			array('name', 'length', 'max' => 50),
			array('name', 'unique', 
                'allowEmpty'    => false,
                'attributeName' => 'name', 
                'caseSensitive' => false,
			    'className'     => 'QUserField',
			    'on' => 'create'
            ),
		    
		    array('type', 'length', 'max' => 255),
			array('storage', 'length', 'max' => 100),
			array('fillpoints', 'length', 'max' => 11),
			array('external, multiple', 'integerOnly' => true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, storage, fillpoints', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // все ссылки на это поле
		    'fieldInstances' => array(self::HAS_MANY, 'QFieldInstance', 'fieldid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'type' => 'Type',
			'storage' => 'Storage',
			'fillpoints' => 'Fillpoints',
			'external' => 'external',
			'multiple' => 'Список',
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
	/*public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('storage', $this->storage, true);
		$criteria->compare('fillpoints', $this->fillpoints, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}*/

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return QUserField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getLabel()
	{
	    //return Yii::t("QuestionaryModule", $this->name.'_label');
	    $label = QuestionaryModule::t($this->name.'_label');
	    if ( $this->multiple )
	    {
	        $label .= ' [список]';
	    }
	    return $label;
	}
	
	/**
	 * Привязать одно из стандартных полей анкеты к объекту (как правило к роли для указания обязательных полей)
	 * 
	 * @param string $objectType - тип объекта к которому привязывается поле: как правило это роль (vacancy)
	 * @param int $objectId
	 * @param string $filling
	 * @throws CException
	 * @return bool
	 */
	public function bindWith($objectType, $objectId, $filling='required')
	{
	    if ( ! $this->id )
	    {
	        throw new CException('object not saved');
	    }
	    
	    $instance = new QFieldInstance();
	    $instance->objecttype = $objectType;
	    $instance->objectid   = $objectId;
	    $instance->fieldid    = $this->id;
	    $instance->filling    = 'required';
	    
	    return $instance->save();
	}
	
	/**
	 * 
	 * @param unknown $objectType
	 * @param unknown $objectId
	 * @return boolean
	 */
	public function isRequiredFor($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->compare('objectid', $objectId);
	    $criteria->compare('fieldid', $this->id);
	    if ( ! $instance = QFieldInstance::model()->find($criteria) )
	    {
	        return false;
	    }
	    if ( $instance->filling === 'required' )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, пусто ли требующее заполнения поле анкеты
	 * 
	 * @param Questionary $questionary - анкета для которой требуется проверить пустое поле
	 * @return bool
	 * 
	 * @todo переименовать storage в relationname и убрать "questionary."
	 */
	public function isEmptyIn($questionary)
	{
	    if ( $questionary->isNewRecord )
	    {// анкета еще не сохранена
	        return true;
	    }
	    $relationName = '';
	    
	    if ( $this->multiple )
	    {// для полей-списков: считаем их пустыми тогда когда нет ни одной записи
	        return ! (bool)$questionary->hasRelated($this->name);
	    }elseif ( $this->external )
	    {// для полей во внешних таблицах
	        $storage      = explode('.', $this->storage);
	        $relationName = $storage[1];
	    }
	    $criteria = $this->createIsEmptyCriteria($questionary->id, $relationName);
	    
	    return (bool)Questionary::model()->exists($criteria);
	}
	
	/**
	 * 
	 * @param int $qid
	 * @return CDbCriteria
	 */
	protected function createIsEmptyCriteria($qid, $relationName=null)
	{
	    $criteria     = new CDbCriteria();
	    $conditions   = array();
	    $emptyOptions = $this->getEmptyOptions();
	    $name         = $this->name;
	    if ( $relationName )
	    {
	        $name = "`{$relationName}`.`{$this->name}`";
	        $criteria->with     = array($relationName);
	        $criteria->together = true;
	    }
	    
	    foreach ( $emptyOptions as $option )
	    {
	        switch ( $option )
	        {
	            case 'null':        $conditions[] = "( {$name} IS NULL )"; break;
	            case 'zero':        $conditions[] = "( {$name} = 0 )"; break;
	            case 'emptystring': $conditions[] = "( {$name} = '' )"; break;
	        }
	    }
	    $criteria->addCondition(implode(' OR ', $conditions));
	    $criteria->compare('t.id', $qid);
	    
	    return $criteria;
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function getEmptyOptions()
	{
	    switch ( $this->type )
	    {
	        case 'text':     return array('null', 'emptystring');
	        case 'textarea': return array('null', 'emptystring'); 
	        case 'select':   return array('null', 'emptystring');
	        case 'slider':   return array('null', 'zero', 'emptystring');
	        case 'phone':    return array('null', 'zero', 'emptystring');
	        case 'url':      return array('null', 'zero', 'emptystring');
	        case 'badge':    return array('null');
	        case 'city':     return array('zero');
	        case 'checkbox': return array('null');
	        case 'toggle':   return array('null');
	        case 'date':     return array('null', 'zero', 'emptystring');
	        case 'country':  return array('null', 'zero');
	    }
	    
	    return array('null', 'emptystring');
	}
}
