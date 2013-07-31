<?php

/**
 * This is the model class for table "{{catalog_filter_instances}}".
 *
 * The followings are the available columns in table '{{catalog_filter_instances}}':
 * @property integer $id
 * @property string $linktype
 * @property integer $linkid
 * @property integer $filterid
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
		return array(
			array('visible', 'numerical', 'integerOnly'=>true),
			array('linkid, filterid', 'length', 'max'=>11),
		    // @todo прописать здесь все возможные типы связей, когда станет точно ясно сколько их
			array('linktype', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, linkid, linktype, filterid, visible', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
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
			'linktype' => 'Тип объекта к которому прикрепляется фильтр',
			'linkid' => 'id объекта к которому прикрепляется фильтр',
			'filterid' => 'Фильтр',
			'visible' => 'Сделать видимым?',
			'order' => 'Порядковый номер',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('linktype',$this->linktype,true);
		$criteria->compare('linkid',$this->linkid,true);
		$criteria->compare('filterid',$this->filterid,true);
		$criteria->compare('visible',$this->visible);
		$criteria->compare('order',$this->order,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}