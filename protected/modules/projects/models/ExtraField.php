<?php

/**
 * This is the model class for table "{{extra_fields}}".
 *
 * The followings are the available columns in table '{{extra_fields}}':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $label
 * @property string $description
 * @property string $timecreated
 * @property string $timemodified
 * 
 * @todo документировать код
 */
class ExtraField extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{extra_fields}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('name', 'filter', 'filter' => 'trim'),
			array('name, type, label', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('timecreated, timemodified', 'length', 'max' => 11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, type, label, description, timecreated, timemodified', 'safe', 'on' => 'search'),
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
            'fieldInstances' => array(self::HAS_MANY, 'ExtraFieldInstance', 'fieldid'),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    return parent::beforeSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'type' => 'Type',
			'label' => 'Label',
			'description' => 'Description',
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
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('type', $this->type, true);
		$criteria->compare('label', $this->label, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('timemodified', $this->timemodified, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExtraField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
