<?php

/**
 * This is the model class for table "{{q_user_fields}}".
 *
 * The followings are the available columns in table '{{q_user_fields}}':
 * @property integer $id
 * @property string $name
 * @property string $storage
 * @property string $fillpoints
 */
class QUserField extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.questionary.models.Questionary');
        parent::init();
    }
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{q_user_fields}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('name', 'length', 'max' => 50),
			array('storage', 'length', 'max' => 100),
			array('fillpoints', 'length', 'max' => 11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, storage, fillpoints', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // все ссылки на это поле
		    'fieldInstances' => array(self::HAS_MANY, 'QFieldInstance', 'fieldid'),
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
			'storage' => 'Storage',
			'fillpoints' => 'Fillpoints',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('storage',$this->storage,true);
		$criteria->compare('fillpoints',$this->fillpoints,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return QUserField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getLabel()
	{
	    return Questionary::model()->getAttributeLabel($this->name);
	}
}
