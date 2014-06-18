<?php

/**
 * This is the model class for table "{{catalog_section_instances}}".
 *
 * The followings are the available columns in table '{{catalog_section_instances}}':
 * @property integer $id
 * @property string $sectionid
 * @property string $objecttype
 * @property string $objectid
 * @property integer $visible
 * @property string $newname
 * @property string $newdescription
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property CatalogSection $section
 */
class CatalogSectionInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{catalog_section_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('sectionid', 'required'),
			array('visible', 'numerical', 'integerOnly' => true),
			array('sectionid, objectid, timecreated, timemodified', 'length', 'max' => 11),
			array('objecttype', 'length', 'max' => 50),
			array('newname, newdescription', 'length', 'max' => 255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sectionid, objecttype, objectid, visible, newname, newdescription, timecreated, timemodified', 
			    'safe', 'on' => 'search',
            ),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'section' => array(self::BELONGS_TO, 'CatalogSection', 'sectionid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sectionid' => 'Раздел',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'visible' => 'Показать?',
			'newname' => 'Newname',
			'newdescription' => 'Newdescription',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('sectionid', $this->sectionid, true);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('visible', $this->visible);
		$criteria->compare('newname', $this->newname, true);
		$criteria->compare('newdescription', $this->newdescription, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('timemodified', $this->timemodified, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CatalogSectionInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий: получить все ссылки на разделы анкет, связанные с определенным объектом
	 *
	 * @param string $objectType
	 * @param string $objectId
	 * @return CategoryInstance
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->compare('objectid', $objectId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
}
