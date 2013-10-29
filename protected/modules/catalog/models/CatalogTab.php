<?php

/**
 * Вкладка в разделе каталога
 *
 * The followings are the available columns in table '{{catalog_tabs}}':
 * @property integer $id
 * @property string $name
 * @property string $shortname
 * @property string $lang
 * @property string $scopeid
 * 
 * Relations:
 * @property SearchScope $scope
 * @property CatalogFilter[] $searchFilters
 * @property CatalogSection[] $sections
 */
class CatalogTab extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.extensions.ESearchScopes.models.*');
        parent::init();
    }
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatalogTab the static model class
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
		return '{{catalog_tabs}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, shortname', 'required'),
			array('name, shortname', 'length', 'max' => 128),
			array('lang', 'length', 'max' => 5),
			array('scopeid', 'length', 'max' => 11),
			// The following rule is used by search().
			array('id, name, shortname, lang, scopeid', 'safe', 'on' => 'search'),
		);
		foreach ( $this->sections as $section1 )
		{
		    
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // прикрепленные к вкладке условия поиска
		    'scope' => array(self::BELONGS_TO, 'SearchScope', 'scopeid'),
		    // Прикрепленные к вкладке фильтры поиска (связь типа "мост")
		    'searchFilters' => array(self::MANY_MANY, 'CatalogFilter',
		        "{{catalog_filter_instances}}(linkid, filterid)",
		        'condition' => "`linktype` = 'tab'"),
		    // разделы, в которых присутствует эта вкладка
		    'sections' => array(self::MANY_MANY, 'CatalogSection',
		        "{{catalog_tab_instances}}(tabid, sectionid)"),
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
			'shortname' => 'Shortname',
			'lang' => 'Lang',
			'scopeid' => 'Scopeid',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('shortname',$this->shortname,true);
		$criteria->compare('lang',$this->lang,true);
		$criteria->compare('scopeid',$this->scopeid,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}