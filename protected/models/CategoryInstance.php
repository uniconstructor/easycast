<?php

/**
 * This is the model class for table "{{category_instances}}".
 *
 * The followings are the available columns in table '{{category_instances}}':
 * @property integer $id
 * @property string $categoryid
 * @property string $objecttype
 * @property string $objectid
 * 
 * Relations:
 * @property Category $category
 */
class CategoryInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{category_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype', 'required'),
			array('categoryid, objectid', 'length', 'max' => 11),
			array('objecttype', 'length', 'max' => 50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, categoryid, objecttype, objectid', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'category' => array(self::BELONGS_TO, 'Category', 'categoryid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'categoryid' => 'Категория',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
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
		$criteria->compare('categoryid', $this->categoryid, true);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CategoryInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованое условие поиска: все  ссылки на категории с определенным типом
	 * @param string $type
	 * @return CategoryInstance
	 */
	public function withType($type)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
            'category' => array(
                'select'    => 'id, type',
                'joinType'  => 'INNER JOIN',
                'condition' => "`category`.`type`='{$type}'",
            ),
	    );
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
}
