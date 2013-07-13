<?php

/**
 * This is the model class for table "{{catalog_tab_instances}}".
 *
 * The followings are the available columns in table '{{catalog_tab_instances}}':
 * @property integer $id
 * @property string $sectionid
 * @property string $parentid
 * @property string $tabid
 * @property string $newname
 * @property string $lang
 * @property integer $visible
 */
class CatalogTabInstance extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatalogTabInstance the static model class
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
		return '{{catalog_tab_instances}}';
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
			array('sectionid, parentid, tabid', 'length', 'max'=>11),
			array('newname', 'length', 'max'=>128),
			array('lang', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sectionid, parentid, tabid, newname, lang, visible', 'safe', 'on'=>'search'),
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
		    'tab' => array(self::BELONGS_TO, 'CatalogTab', 'tabid'),
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
			'parentid' => 'Parentid',
			'tabid' => 'Tabid',
			'newname' => 'Newname',
			'lang' => 'Lang',
			'visible' => 'Visible',
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
		$criteria->compare('parentid',$this->parentid,true);
		$criteria->compare('tabid',$this->tabid,true);
		$criteria->compare('newname',$this->newname,true);
		$criteria->compare('lang',$this->lang,true);
		$criteria->compare('visible',$this->visible);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}