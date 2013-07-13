<?php

/**
 * This is the model class for table "{{allowed_comparison_types}}".
 *
 * The followings are the available columns in table '{{allowed_comparison_types}}':
 * @property integer $id
 * @property string $model
 * @property string $field
 * @property string $fieldlabel
 * @property string $types
 */
class AllowedComparisonType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AllowedComparisonTypes the static model class
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
		return '{{allowed_comparison_types}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model, field', 'required'),
			array('model, field', 'length', 'max'=>128),
			array('fieldlabel, types', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, model, field, fieldlabel, types', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'model' => 'Model',
			'field' => 'Field',
			'fieldlabel' => 'Fieldlabel',
			'types' => 'Types',
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
		$criteria->compare('model',$this->model,true);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('fieldlabel',$this->fieldlabel,true);
		$criteria->compare('types',$this->types,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}