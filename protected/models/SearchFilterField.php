<?php

/**
 * Модель для работы с полем поискового фильтра
 *
 * Таблица '{{search_filter_fields}}':
 * @property integer $id
 * @property string $filterid
 * @property string $name
 * @property string $title
 * @property string $fieldtype
 * @property string $maxvalues
 * @property string $defaultlistid
 * @property string $combine
 * @property string $minvalue
 * @property string $maxvalue
 * @property string $stepvalue
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property SearchFilter        $searchFilter - фильтр поиска в котором находится поле
 * @property EasyList            $defaultList  - список (EasyList) содержащий стандартные значения
 * @property SearchFilterValue[] $fieldValues  - значения, содержащиеся в этом поле 
 *                                               (если поле может содержать несколько значений)
 */
class SearchFilterField extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_filter_fields}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, fieldtype', 'required'),
			array('filterid, maxvalues, defaultlistid, minvalue, maxvalue, stepvalue, 
			    timecreated, timemodified', 'length', 'max' => 11),
			array('name, title, fieldtype', 'length', 'max' => 255),
			array('combine', 'length', 'max' => 20),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // фильтр поиска в котором находится поле
		    'searchFilter' => array(self::BELONGS_TO, 'SearchFilter', 'filterid'),
		    // список (EasyList) содержащий стандартные значения
		    'defaultList'  => array(self::BELONGS_TO, 'EasyList', 'defaultlistid'),
		    // значения, содержащиеся в этом поле (если поле может содержать несколько значений)
		    'fieldValues'  => array(self::HAS_MANY, 'SearchFilterValue', 'filterfieldid'),
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
			'filterid' => 'Filterid',
			'name' => 'Name',
			'title' => 'Title',
			'fieldtype' => 'Fieldtype',
			'maxvalues' => 'Maxvalues',
			'defaultlistid' => 'Defaultlistid',
			'combine' => 'Combine',
			'minvalue' => 'Minvalue',
			'maxvalue' => 'Maxvalue',
			'stepvalue' => 'Stepvalue',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SearchFilterField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Условие поиска: все поля, использующие указанный список значений по умолчанию 
	 * 
	 * @param  int|array $id        - id списка (EasyList) или массив таких id
	 * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return SearchFilterField
	 */
	public function withDefaultListId($id, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`defaultlistid`', $id);
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
}
