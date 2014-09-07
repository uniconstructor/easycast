<?php

/**
 * Модель для работы с значениями настроек
 *
 * Таблица '{{config_values}}':
 * 
 * @property integer $id
 * @property string $configid
 * @property string $type - тип значения:
 *                          'string' - обычная строка (используется в большинстве случаев)
 *                          'option' - стандартное значение из списка 
 *                          (в этом случае поле value должно содержать id элемента ListItem 
 *                          внутри списка, прикрепленного к настройке)
 *                          'json' - массив в формате JSON
 * @property string $value
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property Config $config - настройка к которой привязано значение
 */
class ConfigValue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{config_values}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('configid, timecreated, timemodified', 'length', 'max' => 11),
			array('type', 'length', 'max' => 20),
			array('value', 'length', 'max' => 4095),
		    
			// The following rule is used by search().
			array('id, configid, type, value, timecreated, timemodified', 
			    'safe',
			    'on' => 'search',
            ),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // настройка к которой привязано значение
		    'config' => array(self::BELONGS_TO, 'Config', 'configid'),
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
	            'class'           => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	    );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'configid' => 'Настройка',
			'type' => 'Type',
			'value' => 'Value',
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
		$criteria->compare('configid',$this->configid,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('value',$this->value,true);
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
	 * @return ConfigDefault the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий: получить все значения для одной настройки
	 * @param Config|int $config
	 * @return ConfigValue
	 */
	public function forConfig($config)
	{
	    if ( ! is_object($config) )
	    {// передан id настройки вместо объекта настройки - извлечем весь объект
	        $config = Config::model()->findByPk($config);
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`configid`', $config->id);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
}