<?php

/**
 * Модель для хранения одной характеристики участника
 */
class QActivity extends CActiveRecord
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.questionary.extensions.behaviors.*');
        parent::init();
    }
    
    /**
     * 
     * @param system $className
     * @return Ambigous <CActiveRecord, unknown, multitype:>
     * @return null
     */
	public static function model($className=__CLASS__)
	{
	    Yii::import('application.modules.questionary.extensions.behaviors.*');
		return parent::model($className);
	}
    
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return '{{q_activities}}';
	}
    
	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('questionaryid, timestart, timeend, timecreated', 'length', 'max'=>11),
			array('type, value', 'length', 'max'=>32),
			array('uservalue', 'length', 'max'=>128),
			array('level', 'length', 'max'=>20),
		    
			array('id, questionaryid, type, value, uservalue, level, timestart, timeend, timecreated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return array(
	                'CAdvancedArBehavior',
				        array('class' => 'ext.CAdvancedArBehavior'),
	                'QManageDefaultValuesBehavior',
		                array('class' => 'questionary.extensions.behaviors.QManageDefaultValuesBehavior')
				);
	}

    /**
     * @see parent::beforeSave
     */
    protected function beforeSave()
    {
        if ( $this->isNewRecord )
        {
            $this->timecreated = time();
        }

        return parent::beforeSave();
    }

    /**
     * (non-PHPdoc)
     * @see CModel::attributeLabels()
     */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'questionaryid' => Yii::t('app', 'Questionaryid'),
			'type' => Yii::t('app', 'Type'),
			'value' => Yii::t('app', 'Value'),
			'uservalue' => Yii::t('app', 'Uservalue'),
			'level' => Yii::t('app', 'Level'),
			'timestart' => Yii::t('app', 'Timestart'),
			'timeend' => Yii::t('app', 'Timeend'),
			'timecreated' => Yii::t('app', 'Timecreated'),
		);
	}

	/**
	 * 
	 * @return CActiveDataProvider
	 * @return null
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('questionaryid',$this->questionaryid,true);

		$criteria->compare('type',$this->type,true);

		$criteria->compare('value',$this->value,true);

		$criteria->compare('uservalue',$this->uservalue,true);

		$criteria->compare('level',$this->level,true);

		$criteria->compare('timestart',$this->timestart,true);

		$criteria->compare('timeend',$this->timeend,true);

		$criteria->compare('timecreated',$this->timecreated,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	public function getUservalue()
	{
	    return CHtml::encode($this->uservalue);
	}
	
	public function getName()
	{
	    if ( $this->value == 'custom' )
	    {
	        return $this->Uservalue;
	    }
	
	    if ( $this->scenario == 'view' )
	    {
	        return $this->getDefaultValueForDisplay();
	    }
	
	    return $this->value;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return null
	 */
	public function setName($name)
	{
	    if ( $this->value == 'custom' )
	    {
	        $this->uservalue = strip_tags($name);
	    }else
	    {
	        $this->value = strip_tags($name);
	    }
	}
	
	/**
	 * Определить, изменилось ли значение
	 * @param int $questionaryid
	 * @param string $type - тип поля
	 * @param array $newData - новые значения в сложном поле формы
	 */
	public function valueIsChanged($questionaryid, $type, $newData)
	{
	    switch ( $type )
	    {
	        case 'voicetimbre': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'addchar': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'parodist': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'twin': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'vocaltype': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'sporttype': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'extremaltype': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'trick': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        case 'skill': return $this->simpleActivityIsChanged($questionaryid, $type, $newData); break;
	        default: throw new CException(get_class($this).' valueIsChanged($type, $newData) - incorrect $value parameter: "'.$value.'"');
	    }
	}
	
	/**
	 * Изменилось ли значение простой характеристики (у которой есть только имя: например вид спорта или тембр голоса)
	 * @param int $questionaryid - id анкеты
	 * @param string $type - тип сложного значения
	 * @param array $newData - данные из формы
	 * 
	 * @return bool
	 */
	protected function simpleActivityIsChanged($questionaryid, $type, $newData)
	{
	    if ( (int)$this->countFieldValues($questionaryid, $type) != count($newData) )
	    {// количество значений в старом и новом списке не совпадает - что-то изменилось
	    return true;
	    }
	     
	    foreach ( $newData as $key => $value )
	    {
	        if ( ! $value )
	        {// Добавлен новый элемент - значение поля изменено
	            return true;
	        }
	        $criteria = new CDbCriteria();
	        $criteria->addCondition("questionaryid = :questionaryid");
	        $criteria->addCondition("type = :type");
	         
	        $params = array(
	            ':questionaryid' => $questionaryid,
	            ':type'          => $type);
	         
	        if ( is_numeric($value) )
	        {// введенное пользователем значение
	            $params[':id'] = $value;
	            $criteria->addCondition("id = :id");
	        }else
	        {// значение из стандартного списка
	            $params[':value'] = $value;
	            $criteria->addCondition("value = :value");
	        }
	         
	        $criteria->params = $params;
	         
	        if ( ! $this->exists($criteria) )
	        {// добавлено новое значение
	            return true;
	        }
	    }
	     
	    return false;
	}
	
	/**
	 * Существует ли переданный экземпляр сложного значения внутри поля
	 * @param unknown_type $questionaryid
	 * @param unknown_type $type
	 * @param unknown_type $value
	 * @param unknown_type $standard
	 */
	public function valueIsExists($questionaryid, $type, $value, $standard=true)
	{
	    $criteria = new CDbCriteria();
	    $criteria->addCondition("questionaryid = :questionaryid");
	    $criteria->addCondition("type = :type");
	    $criteria->addCondition("value = :value");
	    
	    $params = array(
	        ':questionaryid' => $questionaryid,
	        ':type'          => $type);
	    
	    if ( $standard )
	    {// ищем стандартные значения
	        $params[':value'] = $value;
	    }else
	   {// ищем пользовательские значения
	       $params[':value'] = 'custom';
	       $criteria->addCondition("uservalue = :uservalue");
	       $params[':uservalue'] = $value;
	    }
	    
	    $criteria->params = $params;
	    
	    return $this->exists($criteria);
	}
	
	/**
	 * Получить все сложные значения указанного поля
	 * @param unknown_type $questionaryid
	 * @param unknown_type $type
	 * @param string $values - all - все значения
	 *                          standard - только стандартные
	 *                          user - только введенные пользователем
	 * @return array
	 */
	public function getFieldValues($questionaryid, $type, $values='all')
	{
	    return $this->findAll($this->fieldValuesCriteria($questionaryid, $type, $values));
	}
	
	/**
	 * Получить количество значений внутри поля
	 * @param unknown_type $questionaryid
	 * @param unknown_type $type
	 * @param string $values - all - все значения
	 *                          standard - только стандартные
	 *                          user - только введенные пользователем
	 */
	public function countFieldValues($questionaryid, $type, $values='all')
	{
	    $criteria = $this->fieldValuesCriteria($questionaryid, $type, $values);
	    return (int)$this->count($criteria);
	}
	
	/**
	 * Удалить все значения из поля одной анкеты
	 * @param unknown_type $questionaryid
	 * @param unknown_type $type
	 * @param string $values - all - все значения
	 *                          standard - только стандартные
	 *                          user - только введенные пользователем
	 * @return number
	 */
	public function deleteFieldValues($questionaryid, $type, $values='all')
	{
	    return $this->deleteAll($this->fieldValuesCriteria($questionaryid, $type, $values));
	}
	
	/**
	 * Получить критерий для выбора всех значений из одного поля анкеты
	 * @param unknown_type $questionaryid
	 * @param unknown_type $type
	 * @param string $values - all - все значения
	 *                          standard - только стандартные
	 *                          user - только введенные пользователем
	 */
	protected function fieldValuesCriteria($questionaryid, $type, $values='all')
	{
	    $params = array();
	    $params[':questionaryid'] = $questionaryid;
	    $params[':type']          = $type;
	    
	    if ( $values == 'standard' OR $values == 'user' )
	    {
	        $params[':value'] = 'custom';
	    }
	     
	    $criteria = new CDbCriteria();
	    $criteria->addCondition("questionaryid = :questionaryid");
	    $criteria->addCondition("type = :type");
	    switch ( $values )
	    {// нужно получить только стандартные или только пользовательские значения
	        case 'user':     $criteria->addCondition("value = :value"); break;
	        case 'standard': $criteria->addCondition("value != :value"); break;
	    }
	    
	    $criteria->params = $params;
	    
	    return $criteria;
	}
}
