<?php

/**
 * This is the model class for table "{{addresses}}".
 *
 * The followings are the available columns in table '{{addresses}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $type
 * @property string $postalcode
 * @property string $countryid
 * @property string $regionid
 * @property string $city
 * @property string $cityid
 * @property string $streettype
 * @property string $streetname
 * @property string $number
 * @property string $housing
 * @property string $gate
 * @property integer $floor
 * @property string $apartment
 * @property string $timecreated
 * @property string $timemodified
 * @property double $latitude
 * @property double $longitude
 * @property string $description
 * @property string $status
 * @property integer $encrypted
 */
class Address extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Address the static model class
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
		return '{{addresses}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('floor, encrypted', 'numerical', 'integerOnly'=>true),
		    
			array('latitude, longitude', 'numerical'),
			array('objecttype', 'length', 'max'=>12),
			array('objectid, type, timecreated, timemodified, countryid, regionid, cityid', 'length', 'max'=>11),
			array('postalcode', 'length', 'max'=>10),
			array('city, streetname, description', 'length', 'max'=>4095),
			
			array('streettype, number, housing, apartment', 'length', 'max'=>16),
			array('gate', 'length', 'max'=>8),
			array('status', 'length', 'max'=>7),
		    
		    array('floor,latitude,longitude,postalcode,city,streetname,
		        description,streettype,number,housing,apartment,gate', 'filter', 'filter'=>'trim'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, objecttype, objectid, type, postalcode, countryid, regionid, city, cityid, streettype, streetname, number, housing, gate, floor, apartment, timecreated, timemodified, latitude, longitude, description, status, encrypted', 'safe', 'on'=>'search'),
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
		    'cityobj'    => array(self::BELONGS_TO, 'CSGeoCity', 'cityid'),
		    'region'  => array(self::BELONGS_TO, 'CSGeoRegion', 'regionid'),
		    'country' => array(self::BELONGS_TO, 'CSGeoCountry', 'countryid'),
		);
	}
	
	/**
	 * @see parent::behaviors
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        )
	    );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// при создании адреса сразу же запишем его тип
	        if ( $this->getScenario() == 'questionary' )
	        {
	            $this->objecttype = 'questionary';
	        }
	        if ( $this->getScenario() == 'event' )
	        {
	            $this->objecttype = 'event';
	        }
        }
	
	    return parent::beforeSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'type' => Yii::t('address','type_label'),
			'postalcode' => Yii::t('address','postalcode_label'),
			'country' => Yii::t('address','country_label'),
		    'countryid' => Yii::t('address','country_label'),
			'region' => Yii::t('address','region_label'),
		    'regionid' => Yii::t('address','region_label'),
			'city' => Yii::t('address','city_label'),
		    'cityid' => Yii::t('address','city_label'),
			'streettype' => Yii::t('address','streettype_label'),
			'streetname' => Yii::t('address','streetname_label'),
			'number' => Yii::t('address','number_label'),
			'housing' => Yii::t('address','housing_label'),
			'gate' => Yii::t('address','gate_label'),
			'floor' => Yii::t('address','floor_label'),
			'apartment' => Yii::t('address','apartment_label(questionary)'),
			'timecreated' => Yii::t('address','timecreated_label'),
			'timemodified' => Yii::t('address','timemodified_label'),
			'latitude' => Yii::t('address','latitude_label'),
			'longitude' => Yii::t('address','longitude_label'),
			'description' => Yii::t('address','description_label'),
			'status' => Yii::t('address','status_label'),
			'encrypted' => Yii::t('address','encrypted_label'),
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
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('postalcode',$this->postalcode,true);
		$criteria->compare('countryid',$this->country,true);
		$criteria->compare('regionid',$this->region,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('cityid',$this->city,true);
		$criteria->compare('streettype',$this->streettype,true);
		$criteria->compare('streetname',$this->streetname,true);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('housing',$this->housing,true);
		$criteria->compare('gate',$this->gate,true);
		$criteria->compare('floor',$this->floor);
		$criteria->compare('apartment',$this->apartment,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('encrypted',$this->encrypted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}