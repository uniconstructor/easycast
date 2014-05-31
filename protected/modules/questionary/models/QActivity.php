<?php

/**
 * Модель для хранения одной характеристики участника
 * 
 * @todo подключить workflow
 * @todo использовать статусы для хранения информации о проекте
 * @todo языковые строки
 */
class QActivity extends CActiveRecord
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType;
    
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('questionary.models.QActivityType');
        Yii::import('questionary.extensions.behaviors.*');
        
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
	 * Cписок сложных полей анкеты в которых указывается только название умения/навыка
	 * Ключ массива - это тип предлагаемых возможных значений (ActivityType)
	 * Значение - класс модели, для которой предлагаются варианты
	 * 
	 * @return array
	 */
	public static function getAllSimpleClasses()
	{
	    return array(
	        'voicetimbre'  => 'QVoiceTimbre',
	        'addchar'      => 'QAddChar',
	        'parodist'     => 'QParodist',
	        'twin'         => 'QTwin',
	        'vocaltype'    => 'QVocalType',
	        'sporttype'    => 'QSportType',
	        'extremaltype' => 'QExtremalType',
	        'trick'        => 'QTrick',
	        'skill'        => 'QSkill',
	    );
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
			array('uservalue', 'length', 'max' => 500),
			array('comment', 'length', 'max' => 4095),
			array('level', 'length', 'max' => 20),
		    array('comment, uservalue, type, value', 'filter', 'filter' => 'trim'),
		    
		    array('type', 'default', 'setOnEmpty' => false, 'value' => $this->_defaultType),
		    
		    array('name', 'length', 'max' => 500),
            array('name', 'filter', 'filter' => 'trim'),
		    array('name', 'required'),
		    
			array('id, questionaryid, type, value, uservalue, level, timestart, 
			    timeend, timecreated, timemodified', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * Именованная группа условий поиска
	 * Получает все записи для выбранной анкеты
	 * 
	 * @param int $id - id анкеты
	 * @return QActivity
	 */
	public function forQuestionary($id)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('questionaryid', $id);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска
	 * Получает все записи определенного типа
	 * @param string $type - краткое название категории умения или навыка
	 * @return QActivity
	 */
	public function withType($type)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('type', $type);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска
	 * Исключает из выборки выбранные значения
	 * @param array $values
	 * @return QActivityType
	 */
	public function except($values)
	{
	    $criteria = new CDbCriteria();
	    $criteria->addNotInCondition('value', $values);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
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
            // функции для работы со стандартными значениями, выбираемыми внутри сложных полей анкеты
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
			'id' => 'ID',
			'type'        => Yii::t('app', 'Type'),
			'value'       => Yii::t('app', 'Value'),
			'level'       => QuestionaryModule::t('level'),
			'timecreated' => Yii::t('coreMessages', 'timecreated'),
			'timecreated' => Yii::t('coreMessages', 'timemodified'),
			'comment' => Yii::t('coreMessages', 'description'),
			'name' => Yii::t('coreMessages', 'title'),
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
	 * Получить название умения или навыка
	 * @return string
	 */
	public function getName()
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('name', $this->type);
	    $criteria->compare('value', $this->value);
	    
	    if ( $this->uservalue OR ! $option = QActivityType::model()->find($criteria) )
	    {// это не значение из списка, оно было добавлено пользователем
	        return $this->uservalue;
	    }
	    // это стандартное значение
	    return $option->translation;
	}
	
	/**
	 * Сохранить название умения или навыка
	 * @param $string $name
	 * @return void
	 */
	public function setName($name)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('name', $this->_defaultType);
	    $criteria->addColumnCondition(array(
	        'value'       => $name,
	        'translation' => $name,
	    ), 'OR');
	    
	    if ( $option = QActivityType::model()->find($criteria) )
	    {// значение выбрано из стандартных
	        $this->value = $option->value;
	    }else
	    {
	        $this->value     = 'custom';
	        $this->uservalue = $name;
	    }
	}
	
	///////////////////////////////////////////
	
	/**
	 * Определить, изменилось ли значение сложного поля
	 * @param int $questionaryid
	 * @param string $type - тип поля
	 * @param array $newData - новые значения в сложном поле формы
	 * 
	 * @deprecated не использовать в новых функциях
	 */
	public function valueIsChanged($questionaryid, $type, $newData)
	{
	    // список сложных полей анкеты в которых указывается только название умения/навыка
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
	 * @return bool
	 * 
	 * @deprecated не использовать в новых функциях
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
	 * @deprecated удалить при рефакторинге
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
	 * 
	 * @deprecated не использовать в новых функциях
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
	 * 
	 * @deprecated не использовать в новых функциях
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
	 * 
	 * @deprecated не использовать в новых функциях
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
	 * 
	 * @deprecated не использовать в новых функциях
	 * @todo написать именованную группу условий если понадобится
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