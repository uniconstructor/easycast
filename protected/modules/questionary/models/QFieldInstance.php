<?php

/**
 * This is the model class for table "{{q_field_instances}}".
 *
 * The followings are the available columns in table '{{q_field_instances}}':
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
 * @todo перенести эту модель в корень проекта, так как она связана с несколькими модулями
 */
class QFieldInstance extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.questionary.QuestionaryModule');
    }
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{q_field_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype', 'required'),
			array('fieldid, objectid, timecreated, timemodified', 'length', 'max' => 11),
			array('objecttype, filling, condition', 'length', 'max' => 50),
			array('data', 'length', 'max' => 1023),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fieldid, objecttype, objectid, filling, condition, data, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'fieldObject' => array(self::BELONGS_TO, 'QUserField', 'fieldid'),
		    // роль, к которой привязаны поля
		    //'vacancy' => array(self::BELONGS_TO, 'EventVacancy', 'objectid'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'fieldid' => 'Поле анкеты',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'filling' => 'Обязательно к заполнению?',
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
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('fieldid', $this->fieldid, true);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('filling', $this->filling, true);
		$criteria->compare('condition', $this->condition, true);
		$criteria->compare('data', $this->data, true);
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
	 * @return QFieldInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Получить название фильтра поиска
	 * @return string
	 */
	public function getName()
	{
	    if ( $this->isNewRecord )
	    {
	        return;
	    }
	    return $this->fieldObject->label;
	}
	
	/**
	 * Получить описание фильтра поиска
	 * @return string
	 */
	public function getDescription()
	{
	    if ( $this->isNewRecord )
	    {
	        return;
	    }
	    return $this->fieldObject->description;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getFillingMode()
	{
	    if ( $this->isNewRecord )
	    {
	        return;
	    }
	    if ( $this->filling === 'required' )
	    {
	        return 'Да';
	    }elseif ( $this->filling === 'recommended' )
	    {
	        return 'Нет';
	    }
	}
}
