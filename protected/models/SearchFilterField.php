<?php

/**
 * Модель для работы с полями фильтра поиска
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
 * @property string $timecreated
 * @property string $timemodified
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, fieldtype', 'required'),
			array('filterid, maxvalues, defaultlistid, timecreated, timemodified', 'length', 'max'=>11),
			array('name, title, fieldtype', 'length', 'max'=>255),
			array('combine', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, filterid, name, title, fieldtype, maxvalues, defaultlistid, combine, timecreated, timemodified', 'safe', 'on'=>'search'),
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
			'filterid' => 'Filterid',
			'name' => 'Name',
			'title' => 'Title',
			'fieldtype' => 'Fieldtype',
			'maxvalues' => 'Maxvalues',
			'defaultlistid' => 'Defaultlistid',
			'combine' => 'Combine',
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
		$criteria->compare('filterid',$this->filterid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('fieldtype',$this->fieldtype,true);
		$criteria->compare('maxvalues',$this->maxvalues,true);
		$criteria->compare('defaultlistid',$this->defaultlistid,true);
		$criteria->compare('combine',$this->combine,true);
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
	 * @return SearchFilterField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
