<?php

/**
 * This is the model class for table "{{geo_cities}}".
 *
 * The followings are the available columns in table '{{geo_cities}}':
 * @property string $id
 * @property string $countryid
 * @property string $regionid
 * @property string $name
 */
class CSGeoCity extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CSGeoCity the static model class
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
		return '{{geo_cities}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('countryid', 'length', 'max'=>11),
			array('regionid', 'length', 'max'=>10),
			array('name', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, countryid, regionid, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'country' => array(self::BELONGS_TO, 'CSGeoCountry', 'countryid'),
		    'region'  => array(self::BELONGS_TO, 'CSGeoRegion', 'regionid'),
		    'questionary' => array(self::HAS_MANY, 'Questionary', 'cityid'),
		    'address' => array(self::HAS_MANY, 'Address', 'cityid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'countryid' => 'Countryid',
			'regionid' => 'Regionid',
			'name' => 'Name',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('countryid',$this->countryid,true);
		$criteria->compare('regionid',$this->regionid,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}