<?php

/**
 * Связь фильтров поиска с другими объектами в базе
 *
 * Таблица '{{catalog_filter_instances}}':
 * @property integer $id
 * @property string $linktype
 * @property integer $linkid
 * @property integer $filterid
 * @property integer $order
 * @property integer $visible
 * 
 * Relations:
 * @property CatalogFilter $filter
 * 
 * @todo автоматически вычислять order при создании записи если он не задан, основываясь
 *       на порядковом номере последнего добавленного к этому объекту фильтра
 * @todo настроить RBAC таким образом, чтобы была возможность:
 *       - в зависимости от роли разрешать прикреплять только определенные фильтры
 *       - в зависимости от роли разрешать искать только по определенным фильтрам
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
			array('visible, order', 'numerical', 'integerOnly' => true),
			array('linkid, filterid', 'length', 'max' => 11),
			array('linktype', 'length', 'max' => 20),
			// The following rule is used by search().
			array('id, linkid, linktype, filterid, order, visible', 'safe', 'on' => 'search'),
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
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
            'order' => '`order` ASC, `id` ASC',
	    );
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return array(
	        'visible' => array(
	            'condition' => $this->getTableAlias(true).'.`visible` = 1',
	        ),
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
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('linktype',$this->linktype,true);
		$criteria->compare('linkid',$this->linkid,true);
		$criteria->compare('filterid',$this->filterid,true);
		$criteria->compare('visible',$this->visible);
		$criteria->compare('order',$this->order,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
	
    /**
	 * Именованая группа условий: 
	 * @param string $objectType
	 * @param string $objectId
	 * @return CatalogFilterInstance
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`linktype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`linkid`', $objectId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
}