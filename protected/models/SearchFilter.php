<?php

/**
 * Модель для работы с фильтрами поиска
 *
 * Таблица '{{search_filters}}':
 * @property integer $id
 * @property string $searchdataid
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $parentid
 * @property string $targetmodel
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property SearchData          $searchData   - условие выборки к которому принадлежит фильтр
 * @property SearchFilterField[] $filterFields - поля, из которых состоит этот фильтр
 */
class SearchFilter extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_filters}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('searchdataid, parentid, timecreated, timemodified', 'length', 'max'=>11),
			array('name, title', 'length', 'max'=>255),
			array('description', 'length', 'max'=>4095),
			array('targetmodel', 'length', 'max'=>128),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // условие выборки к которому принадлежит фильтр
		    'searchData' => array(self::BELONGS_TO, 'SearchData', 'searchdataid'),
		    // поля, из которых состоит этот фильтр
		    'filterFields' => array(self::HAS_MANY, 'SearchFilterField', 'searchdataid'),
		);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    // стандартные условия поиска по датам создания и изменения
	    $timestampScopes = $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	    // собственные условия поиска для модели
	    $modelScopes = array(
	        
	    );
	    return CMap::mergeArray($timestampScopes, $modelScopes);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'searchdataid' => 'Searchdataid',
			'name' => 'Name',
			'title' => 'Title',
			'description' => 'Description',
			'parentid' => 'Parentid',
			'targetmodel' => 'Targetmodel',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SearchFilter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
