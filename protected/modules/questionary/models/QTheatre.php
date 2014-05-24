<?php

/**
 * Информация о театре (для опыта работы)
 *
 * Таблица '{{q_theatres}}':
 * @property integer $id
 * @property string $name
 * @property integer $system
 * @property integer $timecreated
 * @property integer $timemodified
 * 
 * @todo удалить поле "system"
 * @todo прописать связи
 * @todo языковые строки
 * @todo добавить очистку для добавляемого имени театра
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
		return array(
			array('system, search, form, timecreated, timemodified', 'numerical', 'integerOnly' => true),
			array('name', 'length', 'max' => 255),
		    
			array('id, name, system, search, form', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        'CTimestampBehavior' => array(
	            'class'           => 'zii.behaviors.CTimestampBehavior',
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
			'search' => 'Отображать в форме поиска?',
			'form' => 'Предлагать при заполнении формы анкеты?',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('system',$this->system);
		$criteria->compare('search',$this->search);
		$criteria->compare('form',$this->form);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
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
	    $id = intval($name);
	    
        if ( is_numeric($name) AND $this->exists("id = :id", array(':id' => $id)) )
        {// есть театр с таким id
            return $id;
        }elseif ( $theatres = $this->findAll('name = :name', array(':name' => $name)) )
        {// есть театр с таким названием
            $theatre = current($theatres);
            return $theatre->id;
        }
        // такого театра еще нет в базе
        return 0;
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
	    
	    $theatre->name   = $name;
	    $theatre->system = 0;
	    $theatre->save();
	
	    return $theatre->id;
	}
}