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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parentid, timecreated, timemodified', 'length', 'max'=>11),
			array('name', 'length', 'max'=>255),
			array('shortname', 'length', 'max'=>64),
			array('modelid, type', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parentid, name, shortname, modelid, timecreated, timemodified, type', 'safe', 'on'=>'search'),
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
			'parentid' => Yii::t('ESearchScopes.main', 'parentid_label'),
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
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('parentid',$this->parentid,true);
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
        {
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
}