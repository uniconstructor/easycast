<?php

/**
 * This is the model class for table "{{scope_conditions}}".
 *
 * The followings are the available columns in table '{{scope_conditions}}':
 * @property integer $id
 * @property string $scopeid
 * @property string $type
 * @property string $field
 * @property string $value
 * @property string $comparison
 * @property string $combine
 * @property integer $inverse
 * @property string $previousid
 * 
 * @todo remove all criteria types e
 */
class ScopeCondition extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ScopeConditions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{scope_conditions}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type', 'required'),
			array('inverse, rawvalue', 'numerical', 'integerOnly'=>true),
			array('scopeid, previousid', 'length', 'max'=>11),
			array('type', 'length', 'max'=>16),
			array('field', 'length', 'max'=>4095),
			array('with, jointype, jointable, joincondition, joinparams', 'length', 'max'=>4095),
			array('value', 'length', 'max'=>4095),
			array('comparison', 'length', 'max'=>10),
			array('combine', 'length', 'max'=>3),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, scopeid, type, field, value, comparison, combine, inverse, previousid', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		    //'scope' => array(self::BELONGS_TO, 'SearchScope', 'scopeid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'scopeid' => Yii::t('ESearchScopes.main', 'scope'),
			'type' => Yii::t('ESearchScopes.main', 'condition_type'),
			'field' => Yii::t('ESearchScopes.main', 'field'),
			'value' => Yii::t('ESearchScopes.main', 'value'),
			'comparison' => Yii::t('ESearchScopes.main', 'comparsion_type'),
			'combine' => Yii::t('ESearchScopes.main', 'combine_label'),
			'inverse' => Yii::t('ESearchScopes.main', 'inverse_label'),
			'previousid' => 'Previousid',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('scopeid',$this->scopeid,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('comparison',$this->comparison,true);
		$criteria->compare('combine',$this->combine,true);
		$criteria->compare('inverse',$this->inverse);
		$criteria->compare('previousid',$this->previousid,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Combine CDbCriteria from one scope condition
	 * @return CDbCriteria
	 */
	public function getCombinedCriteria()
	{
	    $criteria = new CDbCriteria;
	    
	    if ( $this->with )
	    {// select includes several tables
	        if ( $this->jointype )
	        {
	            $criteria->with = array(
	                $this->with => array(
	                    'joinType' => $this->jointype,
	                )
	            );
	        }else
	       {
	            $criteria->with = array($this->with);
	        }
	    }
	    
	    switch ( $this->type )
	    {
	        case 'field': $criteria = $this->getFieldCriteria($criteria); break;
	        case 'sort': $criteria = $this->getSortCriteria($criteria); break;
	        case 'scope':
	            $scope = SearchScope::model()->findByPk($this->value);
	            $scopeCriteria = $scope->getCombinedCriteria();
	            $criteria->mergeWith($scopeCriteria);
            break;
	        case 'condition': $criteria = $this->getConditionCriteria($criteria); break;
	        case 'serialized': $criteria = $this->getSerializedCriteria($criteria); break;
	    }
	    
	    return $criteria;
	}
	
	/**
	 * @todo implement inverse operator
	 * 
	 * @param CDbCriteria $criteria
	 * @return CDbCriteria
	 * 
	 * @deprecated
	 */
	protected function getFieldCriteria($criteria)
	{
	    if ( $this->rawvalue )
	    {// value contains SQL expressions
    	    switch ( $this->comparison )
    	    {// select the comparison type
    	        case 'equals':
    	            if ( $this->inverse )
    	            {
    	                $criteria->addCondition($this->field.' = '.$this->value);
    	            }else
    	           {
    	                $criteria->addCondition($this->field.' <> '.$this->value);
    	            }
	            break;
    	        case 'startswith': $criteria->addSearchCondition($this->field, $this->value.'%', false); break;
    	        case 'endswith': $criteria->addSearchCondition($this->field, '%'.$this->value, false); break;
    	        case 'contains': $criteria->addSearchCondition($this->field, $this->value); break;
    	        case 'morethen': $criteria->addCondition($this->field.' > '.$this->value); break;
    	        case 'lessthen': $criteria->addCondition($this->field.' < '.$this->value); break;
    	        case 'in': 
    	            $values = unserialize($this->value);
    	            $criteria->addInCondition($this->field, $values);
	            break;
	            // @todo finish other comparison types
    	        case 'isnull':
    	            if ( $this->inverse )
    	            {
    	                $criteria->addCondition($this->field.' IS NOT NULL');
    	            }else
    	           {
    	                $criteria->addCondition($this->field.' IS NULL');
    	            }
	            break;
    	        case 'isempty': break;
    	        case 'isset': break;
    	    }
	    }else
       {
            switch ( $this->comparison )
            {// select the comparison type
                case 'equals':
                    if ( $this->inverse )
                    {
                        $criteria->compare($this->field, '<>'.$this->value);
                    }else
                 {
                        $criteria->addColumnCondition(array($this->field => $this->value));
                    }
                break;
    	        case 'startswith': $criteria->addSearchCondition($this->field, $this->value.'%', false); break;
    	        case 'endswith': $criteria->addSearchCondition($this->field, '%'.$this->value, false); break;
    	        case 'contains': $criteria->addSearchCondition($this->field, $this->value); break;
    	        case 'morethen': $criteria->compare($this->field, '>'.$this->value); break;
    	        case 'lessthen': $criteria->compare($this->field, '<'.$this->value); break;
    	        case 'in': 
    	            $values = unserialize($this->value);
    	            $criteria->addInCondition($this->field, $values);
	            break;
	            // @todo finish other comparison types
    	        case 'isnull':
    	            if ( $this->inverse )
    	            {
    	                $criteria->addCondition($this->field.' IS NOT NULL');
    	            }else
    	           {
    	                $criteria->addCondition($this->field.' IS NULL');
    	            }
	            break;
    	        case 'isempty': break;
    	        case 'isset': break;
            }
        }
        
        return $criteria;
	}
	
	/**
	 *
	 * @param CDbCriteria $criteria
	 * @return CDbCriteria
	 * 
	 * @deprecated
	 */
	protected function getSortCriteria($criteria)
	{
	     $column = '`t`.`'.$this->field.'`';
	     
	     if ( $criteria->order )
	     {
	         $criteria->order .= ', '.$column.' '.$this->value;
	     }else
	    {
	         $criteria->order .= ' '.$column.' '.$this->value;
	     }
	     
	     return $criteria;
	}
	
	/**
	 *
	 * @param CDbCriteria $criteria
	 * @return CDbCriteria
	 * 
	 * @todo throw exceptions in case of error
	 * 
	 * @deprecated
	 */
	protected function getConditionCriteria($criteria)
	{
	    $criteria->addCondition($this->field);
	    
	    if ( $values = unserialize($this->value) )
	    {
	        foreach ( $values as $id=>$value )
	        {
	            if ( ! isset($criteria->params[$id]) )
	            {// if params for SQL not set externally - set 'em to default
	                $criteria->params[$id] = $value;
	            }
	        }
	    }
	    
	    return $criteria;
	}
	
	/**
	 *
	 * @param CDbCriteria $criteria
	 * @return CDbCriteria
	 */
	protected function getSerializedCriteria($criteria)
	{
	    if ( $newCriteria = unserialize($this->value) )
	    {
	        //$params = $newCriteria->params;
	        //CVarDumper::dump($params, 10, true);die(121212121);
	        //return new CDbCriteria($params);
	        return $newCriteria;
	    }
	    
	    return $criteria;
	}
}