<?php

/**
 * Модель для хранения одной характеристики участника
 * Таблица этой модели не содержит поля "дата изменения", потому принцип работы со всеми
 * значениями класса QActivity не предусматривает их изменения: при редактировании таких полей
 * весь старый набор значений удаляется и заменяется новым
 * 
 * @todo языковые строки
 */
class QActivity extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('questionary.models.QActivityType');
        Yii::import('application.modules.questionary.extensions.behaviors.*');
        
        parent::init();
    }
    
    /**
     * @param system $className
     * @return QActivity
     */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
	/**
	 * @see CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return '{{q_activities}}';
	}
    
	/**
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('questionaryid, timestart, timeend, timecreated, timemodified', 'length', 'max' => 11),
			array('type, value', 'length', 'max' => 32),
			array('uservalue', 'length', 'max' => 128),
			array('level', 'length', 'max' => 20),
		    
			array('id, questionaryid, type, value, uservalue, level, timestart, 
			    timeend, timecreated, timemodified', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		);
	}

	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return array(
            'CAdvancedArBehavior' => array(
              'class' => 'ext.CAdvancedArBehavior',
            ),
            'QManageDefaultValuesBehavior' => array(
              'class' => 'questionary.extensions.behaviors.QManageDefaultValuesBehavior',
            ),
		    // автоматическое заполнение дат создания и изменения
		    'CTimestampBehavior' => array(
		        'class'           => 'zii.behaviors.CTimestampBehavior',
		        'createAttribute' => 'timecreated',
		        'updateAttribute' => 'timemodified',
		    ),
		);
	}

    /**
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
			'timecreated' => Yii::t('app', 'Timemodified'),
		);
	}

	/**
	 * 
	 * @return CActiveDataProvider
	 * @return null
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('uservalue',$this->uservalue,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}
	
	/**
	 * Получить добавленное пользователем название умения или навыка
	 * @return string
	 */
	public function getUservalue()
	{
	    return CHtml::encode($this->uservalue);
	}
	
	/**
	 * Получить название умения или навыка
	 * @return string
	 */
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
	 * Сохранить название умения/навыка: стандартное или введенное пользователем
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
	 * Определить, изменилось ли значение сложного поля
	 * @param int $questionaryid
	 * @param string $type - тип поля
	 * @param array $newData - новые значения в сложном поле формы
	 */
	public function valueIsChanged($questionaryid, $type, $newData)
	{
	    $activities = array('voicetimbre', 'addchar', 'parodist', 'twin', 'vocaltype', 'sporttype', 
	       'extremaltype', 'trick', 'skill');
	    if ( ! in_array($type, $activities) )
	    {// неизвестный тип навыка
	        throw new CException(get_class($this).
	            ' valueIsChanged($type, $newData) - incorrect $value parameter: "'.$value.'"');
	    }
	    return $this->simpleActivityIsChanged($questionaryid, $type, $newData);
	}
	
	/**
	 * Изменилось ли значение простой характеристики 
	 * (умение или навык у которой есть только название и нет уровня владения: 
	 * например вид спорта или тембр голоса)
	 * 
	 * @param int $questionaryid - id анкеты
	 * @param string $type - тип сложного значения
	 * @param array $newData - новый список значений в поле формы
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
	    {// просматриваем все значения из поля анкеты
	        if ( ! $value )
	        {// Добавлен новый элемент - значение поля изменено
	            return true;
	        }
	        $criteria = new CDbCriteria();
	        $criteria->compare('questionaryid', $questionaryid);
	        $criteria->compare('type', $type);
	        
	        if ( is_numeric($value) )
	        {// значение из стандартного списка
	            $criteria->compare('id', $value);
	        }else
	        {// введенное пользователем значение
	            $criteria->compare('value', $value);
	        }
	        if ( ! $this->exists($criteria) )
	        {// добавлено новое значение: данные в поле редактировались
	            return true;
	        }
	    }
	    // данные в поле не редактировались
	    return false;
	}
	
	/**
	 * Есть ли название переданного умения или навыка в списке значений сложного поля?
	 * 
	 * @param int $questionaryid
	 * @param string $type
	 * @param string $value
	 * @param bool $standard
	 * 
	 * @return bool
	 * 
	 * @todo переименовать в activityExists
	 */
	public function valueIsExists($questionaryid, $type, $value, $standard=true)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('questionaryid', $questionaryid);
	    $criteria->compare('type', $type);
	    $criteria->compare('value', $value);
	    
	    if ( $standard )
	    {// ищем стандартные значения
	        $criteria->compare('value', $value);
	    }else
	    {// ищем пользовательские значения
            $criteria->compare('value', 'custom');
            $criteria->compare('uservalue', $value);
	    }
	    
	    return $this->exists($criteria);
	}
	
	/**
	 * Получить все умения или навыки из указанного поля
	 * 
	 * @param int $questionaryid
	 * @param string $type - тип навыка
	 * @param string $values - какие значения получать?
	 *     all - все значения
	 *     standard - только стандартные
	 *     user - только введенные пользователем
	 * @return array
	 */
	public function getFieldValues($questionaryid, $type, $values='all')
	{
	    return $this->findAll($this->fieldValuesCriteria($questionaryid, $type, $values));
	}
	
	/**
	 * Получить количество умений/навыков внутри поля
	 * 
	 * @param int $questionaryid
	 * @param string $type
	 * @param string $values - какие значения получать?
	 *     all - все значения
	 *     standard - только стандартные
	 *     user - только введенные пользователем
	 * @return int
	 */
	public function countFieldValues($questionaryid, $type, $values='all')
	{
	    $criteria = $this->fieldValuesCriteria($questionaryid, $type, $values);
	    return (int)$this->count($criteria);
	}
	
	/**
	 * Удалить все значения из поля одной анкеты
	 * 
	 * @param int $questionaryid
	 * @param string $type
	 * @param string $values - какие значения получать?
	 *     all - все значения
	 *     standard - только стандартные
	 *     user - только введенные пользователем
	 * @return int
	 */
	public function deleteFieldValues($questionaryid, $type, $values='all')
	{
	    return $this->deleteAll($this->fieldValuesCriteria($questionaryid, $type, $values));
	}
	
	/**
	 * Получить критерий для выбора всех значений из одного поля анкеты
	 * 
	 * @param int $questionaryid
	 * @param string $type
	 * @param string $values - какие значения получать?
	 *     all - все значения
	 *     standard - только стандартные
	 *     user - только введенные пользователем
	 * @return CDbCriteria
	 */
	protected function fieldValuesCriteria($questionaryid, $type, $values='all')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('questionaryid', $questionaryid);
	    $criteria->compare('type', $type);
	    
	    switch ( $values )
	    {// нужно получить только стандартные или только пользовательские значения
	        case 'user':     $criteria->compare('value', 'custom'); break;
	        case 'standard': $criteria->compare('value', '<>custom'); break;
	    }
	    
	    return $criteria;
	}
}