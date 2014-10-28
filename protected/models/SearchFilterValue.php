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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('objecttype, objectfield, objectvalue', 'required'),
			array('filterfieldid, timecreated, timemodified', 'length', 'max'=>11),
			array('title, objectvalue', 'length', 'max'=>255),
			array('combine, prefix, operation', 'length', 'max'=>20),
			array('objecttype, objectfield', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, filterfieldid, title, combine, objecttype, objectfield, objectvalue, prefix, operation, timecreated, timemodified', 'safe', 'on'=>'search'),
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
		$criteria->compare('filterfieldid',$this->filterfieldid,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('combine',$this->combine,true);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectfield',$this->objectfield,true);
		$criteria->compare('objectvalue',$this->objectvalue,true);
		$criteria->compare('prefix',$this->prefix,true);
		$criteria->compare('operation',$this->operation,true);
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
	 * @return SearchFilterValue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
