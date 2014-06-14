<?php

/**
 * This is the model class for table "{{categories}}".
 *
 * The followings are the available columns in table '{{categories}}':
 * @property integer $id
 * @property string $parentid
 * @property string $type
 * @property string $name
 * @property string $description
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
		    'parent' => array(self::BELONGS_TO, 'Category', 'parentid'),
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
			'type' => 'Type',
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
		// @todo Please modify the following code to remove attributes that should not be searched.

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
	 * Именованная группа условий: получить все дочерние категории (глубина - 1 уровень) 
	 * @param int $parentId
	 * @return Category
	 */
	public function childrenFor($parentId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('parentid', $parentId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
}
