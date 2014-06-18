<?php

/**
 * Модель для работы с разделами и категориями объектов
 *
 * The followings are the available columns in table '{{categories}}':
 * @property integer $id
 * @property string $parentid
 * @property string $type
 * @property string $name
 * @property string $description
 * 
 * Relations:
 * @property Category $parent
 * @property CategoryInstance[] $instances
 * 
 * @todo добавить maxinstances/mininstances
 */
class Category extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{categories}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('type, name', 'required'),
			array('parentid', 'length', 'max'=>11),
			array('type', 'length', 'max'=>50),
			array('name, description', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parentid, type, name, description', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // родительская категория
		    'parent' => array(self::BELONGS_TO, 'Category', 'parentid'),
		    // все ссылки на эту категорию
		    'instances' => array(self::HAS_MANY, 'CategoryInstance', 'categoryid'),
		    // дополнительные поля, которые находятся в этой категории
		    'extraFields' => array(self::MANY_MANY, 'ExtraField', "{{extra_field_instances}}(objectid, fieldid)",
		        'condition' => "`objecttype` = 'category'",
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
			'parentid' => 'Верхний раздел',
			'type' => 'Что содержит?',
			'name' => 'Название',
			'description' => 'Описание',
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
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('parentid', $this->parentid,true);
		$criteria->compare('type', $this->type,true);
		$criteria->compare('name', $this->name,true);
		$criteria->compare('description', $this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Category the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованная группа условий: получить все дочерние категории от указанной
	 * (не рекурсивно, глубина - 1 уровень)
	 * 
	 * @param int|array - $parentIds один или несколько id родительских категорий
	 * @return Category
	 */
	public function forParent($parentIds)
	{
	    $criteria = new CDbCriteria();
	    if ( ! is_array($parentIds) AND is_numeric($parentIds) )
	    {
	        $parentId = intval($parentIds);
	        $criteria->compare('parentid', $parentId);
	    }elseif ( is_array($parentIds) )
	    {
	        $criteria->addInCondition('parentid', $parentIds);
	    }else
	    {
	        throw new CException('Неправильный формат параметра для условия forParent()');
	    }
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все категории с содержимым определенного типа
	 * 
	 * @param array|string $types
	 * @return Category
	 */
	public function withType($types)
	{
	    $criteria = new CDbCriteria();
	    if ( ! is_array($types) )
	    {
	        $types = array($types);
	    }
	    $criteria->addInCondition('type', $types);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Получить список возможных вариантов содержимого для категории
	 * @return array
	 * 
	 * @todo перенести в список стандартных значений
	 */
	public function getTypeOptions()
	{
	    return array(
	        'categories' => 'Другие категории',
	        'sections' => 'Разделы для анкет (или условия поиска)',
	        'userfields' => 'Поля анкеты',
	        'extrafields' => 'Поля заявки',
	        'tags' => 'Теги',
	    );
	}
	
	/**
	 * 
	 * @return string
	 * 
	 * @todo перенести в список стандартных значений
	 */
	public function getTypeOption()
	{
	    $types = $this->getTypeOptions();
	    return $types[$this->type];
	}
}
