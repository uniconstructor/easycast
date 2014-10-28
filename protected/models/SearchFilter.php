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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('searchdataid, parentid, timecreated, timemodified', 'length', 'max'=>11),
			array('name, title', 'length', 'max'=>255),
			array('description', 'length', 'max'=>4095),
			array('targetmodel', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, searchdataid, name, title, description, parentid, targetmodel, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('searchdataid',$this->searchdataid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('parentid',$this->parentid,true);
		$criteria->compare('targetmodel',$this->targetmodel,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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
