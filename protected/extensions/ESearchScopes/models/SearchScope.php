<?php

/**
 * This is the model class for table "{{search_scopes}}".
 *
 * The followings are the available columns in table '{{search_scopes}}':
 * @property integer $id
 * @property string $name
 * @property string $shortname
 * @property string $modelid
 * @property string $timecreated
 * @property string $timemodified
 * @property string $type
 * @property array $scopeConditions
 */
class SearchScope extends CActiveRecord
{
    /**
     * @var int - id модели questionary в таблице search_scope_models
     *            (взято за константу потому что она там одна и лежит там только для Третьей Нормальной Формы (3NF))
     */
    const QMODEL_ID = 1;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SearchScopes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'timecreated',
                'updateAttribute' => 'timemodified',
            ));
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_scopes}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    foreach ( $this->scopeConditions as $condition )
	    {
	        $condition->delete();
	    }
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('timecreated, timemodified', 'length', 'max'=>11),
			array('name', 'length', 'max'=>255),
			array('shortname', 'length', 'max'=>64),
			array('modelid, type', 'length', 'max'=>128),
			// The following rule is used by search().
			array('id, name, shortname, modelid, timecreated, timemodified, type', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // @todo replace with proper method, getting scopes by chain
		    'scopeConditions' => array(self::HAS_MANY, 'ScopeCondition', 'scopeid', 'order' => '`previousid` ASC, `id` ASC'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => Yii::t('ESearchScopes.main', 'name_label'),
			'shortname' => Yii::t('ESearchScopes.main', 'shortname_label'),
			'model' => Yii::t('ESearchScopes.main', 'model_label'),
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'type' => Yii::t('ESearchScopes.main', 'scope_type'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 * 
	 * @todo refactoring:delete this metrhod 
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('shortname',$this->shortname,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('type',$this->type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Combine CDbCriteria from scope conditions
	 * @param CDbCriteria $otherCriteria - other criteria to merge with
	 * @param array $externalParams - additio
	 * @return CDbCriteria
	 */
	public function getCombinedCriteria($otherCriteria=null, $combineOperator='AND')
	{
        $criteria = new CDbCriteria();
	    
        foreach ( $this->scopeConditions as $condition )
        {/* @var $condition ScopeCondition */
            $operator = $condition->combine;
            $conditionCriteria = $condition->getCombinedCriteria();
            $criteria->mergeWith($conditionCriteria, $operator);
        }
        
        if ( $otherCriteria )
        {
            $criteria->mergeWith($otherCriteria, $combineOperator);
        }
        
        return $criteria;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getSearchData()
	{
	    if ( ! $this->scopeConditions )
	    {
	        throw new CException('No condition attached to this scope: cannot extract searchData');
	    }
	    if ( count($this->scopeConditions) > 1 )
	    {
	        throw new CException('More than one condition attached to this scope: cannot extract searchData');
	    }
	    
	    $condition = current($this->scopeConditions);
	    return $condition->getSearchDataAsArray();
	}
	
	/**
	 * Serialize and save custom form search data
	 * @param array $newData - data from search form
	 * @throws CException
	 * @return void
	 */
	public function setSearchData($newData)
	{
	    if ( ! $this->scopeConditions )
	    {
	        throw new CException('No condition attached to this scope: cannot save searchData');
	    }
	    if ( count($this->scopeConditions) > 1 )
	    {
	        throw new CException('More than one condition attached to this scope: cannot save searchData');
	    }
	    
	    $condition = current($this->scopeConditions);
	    return $condition->setSearchData($newData);
	}
}