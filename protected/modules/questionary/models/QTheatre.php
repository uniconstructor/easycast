<?php

/**
 * This is the model class for table "{{q_theatres}}".
 *
 * The followings are the available columns in table '{{q_theatres}}':
 * @property integer $id
 * @property string $name
 * @property integer $system
 */
class QTheatre extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QTheatre the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{q_theatres}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('system', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, system', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'system' => 'System',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('system',$this->system);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Проверить, есть ли в нашей базе театр с таким названием
	 * @param string $name - название театра
	 *
	 * @return bool|int - id найденного театра (если он существует) или false
	 *                    если в нашей базе нет театра с таким названием
	 */
	public function theatreExists($name)
	{
	    if ( ! $theatres = $this->findAll('name = :name', array(':name' => $name)) )
	    {
	        return false;
	    }
	
	    $theatre = current($theatres);
	    return $theatre->id;
	}
	
	/**
	 * Добавить к нашему списку театров новый (введенный пользователем)
	 * @param string $name - название театра
	 *
	 * @return int - id новой записи
	 */
	public function addUserTheatre($name)
	{
	    $theatre = new QTheatre;
	    
	    $theatre->name   = CHtml::encode($name);
	    $theatre->system = 0;
	
	    $theatre->save();
	
	    return $theatre->id;
	}
}