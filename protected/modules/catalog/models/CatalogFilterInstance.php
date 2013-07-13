<?php

/**
 * This is the model class for table "{{catalog_filter_instances}}".
 *
 * The followings are the available columns in table '{{catalog_filter_instances}}':
 * @property integer $id
 * @property string $sectionid
 * @property string $filterid
 * @property integer $visible
 */
class CatalogFilterInstance extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatalogFilterInstance the static model class
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
		return '{{catalog_filter_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('visible', 'numerical', 'integerOnly'=>true),
			array('sectionid, filterid', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sectionid, filterid, visible', 'safe', 'on'=>'search'),
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
		    'filter' => array(self::BELONGS_TO, 'CatalogFilter', 'filterid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sectionid' => 'Sectionid',
			'filterid' => 'Filterid',
			'type' => 'Type',
			'visible' => 'Visible',
			'customvalues' => 'Customvalues',
			'default' => 'Default',
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
		$criteria->compare('sectionid',$this->sectionid,true);
		$criteria->compare('filterid',$this->filterid,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('visible',$this->visible);
		$criteria->compare('customvalues',$this->customvalues);
		$criteria->compare('default',$this->default,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}