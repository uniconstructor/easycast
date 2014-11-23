<?php

/**
 * Модель для работы со значениями в полях поисковых фильтров
 *
 * Таблица '{{search_filter_values}}':
 * @property integer $id
 * @property string $filterfieldid
 * @property string $title
 * @property string $combine
 * @property string $objecttype
 * @property string $objectfield
 * @property string $objectvalue
 * @property string $prefix
 * @property string $operation
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property SearchFilterField $filterField - поле фильтра поиска которому принадлежит это значение
 */
class SearchFilterValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_filter_values}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype, objectfield, objectvalue', 'required'),
			array('filterfieldid, timecreated, timemodified', 'length', 'max'=>11),
			array('title, objectvalue', 'length', 'max'=>255),
			array('combine, prefix, operation', 'length', 'max'=>20),
			array('objecttype, objectfield', 'length', 'max'=>128),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // поле фильтра поиска которому принадлежит это значение
		    'filterField' => array(self::BELONGS_TO, 'SearchFilterField', 'filterfieldid'),
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
			'filterfieldid' => 'Filterfieldid',
			'title' => 'Title',
			'combine' => 'Combine',
			'objecttype' => 'Objecttype',
			'objectfield' => 'Objectfield',
			'objectvalue' => 'Objectvalue',
			'prefix' => 'Prefix',
			'operation' => 'Operation',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SearchFilterValue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
