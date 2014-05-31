<?php

/**
 * This is the model class for table "{{extra_field_instances}}".
 *
 * The followings are the available columns in table '{{extra_field_instances}}':
 * @property integer $id
 * @property string $fieldid
 * @property string $objecttype
 * @property string $objectid
 * @property string $filling
 * @property string $condition
 * @property string $data
 * @property string $timecreated
 * @property string $timemodified
 * 
 * @todo документировать код
 */
class ExtraFieldInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{extra_field_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype', 'required'),
			array('objecttype, filling, condition', 'length', 'max'=>50),
			array('fieldid, objectid, timecreated, timemodified', 'length', 'max'=>11),
			array('data', 'length', 'max'=>1023),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fieldid, objecttype, objectid, filling, condition, data, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	    );
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'extrafield' => array(self::BELONGS_TO, 'ExtraField', 'fieldid'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'fieldid' => 'Fieldid',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'filling' => 'Filling',
			'condition' => 'Condition',
			'data' => 'Data',
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
		$criteria->compare('fieldid',$this->fieldid,true);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('filling',$this->filling,true);
		$criteria->compare('condition',$this->condition,true);
		$criteria->compare('data',$this->data,true);
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
	 * @return ExtraFieldInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
