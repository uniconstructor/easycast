<?php

/**
 * This is the model class for table "{{catalog_filters}}".
 *
 * The followings are the available columns in table '{{catalog_filters}}':
 * @property integer $id
 * @property string $widgetclass
 * @property string $handlerclass
 * @property string $name
 * @property string $shortname
 */
class CatalogFilter extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CatalogFilter the static model class
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
		return '{{catalog_filters}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('shortname, widgetclass, handlerclass', 'required'),
			array('name, shortname, widgetclass, handlerclass', 'length', 'max'=>255),
			// The following rule is used by search().
			array('id, name, shortname, widgetclass, handlerclass', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // связь фильтров с разделами каталога, вкладками, и другими объектами, 
		    // к которым можно прикрепить критерии поиска
		    'instances' => array(self::HAS_MANY, 'CatalogFilterInstance', 'filterid'),
		    
		    // разделы каталога, в которых используется этот фильтр
		    // @todo работа этой связи не протестирована
		    'sections' => array(self::MANY_MANY, 'CatalogSection',
		        "{{catalog_filter_instances}}(filterid, linkid)",
		        'condition' => "`linktype` = 'section'"),
		    // вкладки в разделах, в которых используется этот фильтр
		    // @todo работа этой связи не протестирована
		    'tabs' => array(self::MANY_MANY, 'CatalogTab',
		        "{{catalog_filter_instances}}(filterid, linkid)",
		        'condition' => "`linktype` = 'tab'"),
		    // вакансии (роли), в которых используется этот фильтр
		    // @todo работа этой связи не протестирована
		    'vacancies' => array(self::MANY_MANY, 'CatalogTab',
		        "{{catalog_filter_instances}}(filterid, linkid)",
		        'condition' => "`linktype` = 'vacancy'"),
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
			'shortname' => 'Name',
			'widgetclass' => 'Класс виджета, отображающего этот фильтр',
			'handlerclass' => 'Класс обработчика для этого фильтра',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('shortname',$this->shortname,true);
		$criteria->compare('widgetclass',$this->widgetclass,true);
		$criteria->compare('handlerclass',$this->handlerclass,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}