<?php

/**
 * Тип деятельности.
 * Используется для составления списков стандартных значений при заполнении анкеты.
 *
 * Хранит все возможные стандартные типы умений, навыков и характеристик (типы вокала, музыкальные инструменты,
 * стили танца, и т. п.).
 * Добавленные пользователем собственные (нестандартные) значения не хранятся здесь - они находятся в QActivity
 *
 * @todo в будущем следует реализовать админский интерфейс, в котором можно будет
 *       добавлять дополнительные типы деятельности из пользовательских.
 *       (когда пользовательских наберется достаточно много)
 *       при этом у пользовательского значения (QActivity) слово custom в поле value,
 *       и заменяется на введенное админом короткое обозначение.
 *       После этого по базе ищутся все использования этого значения и заменяются на стандартные
 * @todo прописать relations когда все введенные пользователем значения будут перенесены в эту таблицу
 * @todo убрать поле "translation" и "language" - они были добавлены как временное решение.
 *       Вместо них следует реализовать нормальную поддержку разных языков сайта, но поскольку
 *       мы планируем оставить участникам возможность вводить свои значения в некоторые поля,
 *       а их анкеты нужно будет отображать на разных языках - то нужно будет решить вопрос с переводом этих данных.
 *       Отказаться от перевода или использовать google translate крайне нежелательно:
 *       мы планируем работать с иностранными заказчиками, которые могут попросить, например,
 *       моделей рассказать о себе, и нужно будет иметь возможность предоставить эту информацию на любом языке.
 *       Возможные решения: использовать в модуле анкеты другой источник языковых строк для перевода 
 *       (CDbMessageSource вместо CPhpMessageSource) и дать админам инструмент для дополнения строк перевода
 * @todo добавить phpdoc-комментарии
 */
class QActivityType extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('questionary.models.QActivity');
        parent::init();
    }
    
    /**
     * @param system $className
     * @return QActivityType
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
		return '{{q_activity_types}}';
	}

	/**
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
		    array('timecreated, timemodified', 'length', 'max' => 11),
		    
			array('name', 'length', 'max' => 20),
		    array('name', 'filter', 'filter' => 'trim'),
			array('name', 'required'),
		    
			array('value', 'length', 'max' => 32),
		    array('value', 'filter', 'filter' => 'trim'),
		    array('value', 'filter', 'filter' => 'strtolower'),
		    array('value', 'required'),
		    
			array('translation', 'length', 'max' => 255),
		    array('translation', 'filter', 'filter' => 'trim'),
		    array('translation', 'required'),
		    
		    array('language', 'default', 'value' => 'ru'),
		    
		    array('form, search', 'numerical', 'integerOnly' => true),
			array('id, name, value, timecreated, timemodified', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * Именованная группа условий поиска
	 * Получает все записи определенного типа
	 * @param string $type - краткое название категории умения или навыка
	 * @return QActivityType
	 */
	public function forActivity($name)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('name', $name);
	    $criteria->order = '`translation` ASC';
	    
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
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Тип значения',
			'value' => 'Короткое название. Без пробелов и кавычек, только латинскими буквами, лучше всего перевод на английский. Например: "Горные лыжи" => "ski". Если слов несколько то их нужно разделять_подчеркиванием.',
			'translation' => 'Текст (отображается пользователю)',
		    'form' => 'Предлагать при заполнении анкеты?',
		    'search' => 'Предлагать в форме поиска?',
		);
	}

	/**
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name, true);
		$criteria->compare('value',$this->value, true);
		$criteria->compare('timecreated',$this->timecreated, true);
		$criteria->compare('timemodified',$this->timemodified, true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        if ( ! $this->isUniqueShortName($this->value) )
	        {// короткое название не уникально - так не должно быть
	            // @todo перенести эту проверку в rules()
    	        throw new CDbException('New short value is not unique');
    	        return false;
	        }
	    }else
        {// запись обновляется
	        // старые данные записи
	        $old = $this::model()->findByPk($this->id);
	        // новые данные записи
	        $new = $this;
	        
	        if ( $old->value != $new->value )
	        {// короткое название значения изменилось - обновим все связанные записи
    	        if ( ! $this->isUniqueShortName($new->value) )
    	        {// короткое название не уникально - так не должно быть
        	        throw new CDbException('New short value is not unique');
        	        return false;
    	        }
    	        if ( ! $this->updateRelatedActivities($old->value, $new->value) )
    	        {// не удалось обновить связанные записи
        	        throw new CDbException('Unable to update related activities');
        	        return false;
    	        }
	        }
	    }
	    
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    // После добавления нового стандартного значения проверим, было ли оно введено пользователем раньше
	    // и если да - то обновим анкеты таких пользователей
	    
	    if ( $this->isNewRecord )
	    {// обновляем существующие анкеты только если новое стандартное значение добавляется
	        $criteria = new CDbCriteria();
	        $criteria->compare('type', $this->name);
	        $criteria->compare('value', 'custom');
	        $criteria->compare('uservalue', $this->translation);
	        // находим все записи с таким же значением
	        $activities = QActivity::model()->findAll($criteria);
	        
	        foreach ( $activities as $activity )
	        {// нашлись такие записи - больше не считаем их нестандартными 
	            $activity->value = $this->value;
	            $activity->uservalue = '';
	            $activity->save();
	        }
	    }
	    
	    parent::afterSave();
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    if ( $this->hasRelatedActivities() )
	    {// запрещаем удалять стандартное значение если оно используется хотя бы в одной анкете
	        return false;
	    }
	    
	    return parent::beforeDelete();
	}
	
	/**
	 * Получить список стандартных вариантов для одного 
	 * вида умения или навыка (для использования в select-элементах)
	 * 
	 * @param string $name - краткое название типа деятельности (вид спорта[sporttype], вокал[vocaltype], и т. п.)
	 * @return array
	 */
	public function activityVariants($name)
	{
	    $result   = array();
	    $criteria = new CDbCriteria();
	    $criteria->compare('name', $name);
	    $criteria->order = '`translation` ASC';
	    
	    if ( ! $variants = $this->findAll($criteria) )
	    {// ищем все стандартные варианты для умения/навыка
	        return array();
	    }
	    foreach ( $variants as $variant )
	    {
	        $result[$variant->value] = $variant->translation;
	    }
	    
	    return $result;
	}
	
	/**
	 * Определить, является ли переданный экземпляр сложного значения стандартным
     * (то есть установленное системой а не введенное пользователем)
	 * @param string $type - тип проверяемого значения
	 * @param string $value - само значение
     *
     * @return bool
	 */
	public function isStandardComplexValue($type, $value)
	{
	    $variants = $this->activityVariants($type);
	    if ( ! isset($variants[$value]) )
	    {
	        return false;
	    }
	    return true;
	}
	
	/**
	 * Обновить все ранее введенные значения в анкетах при изменении стандартного значения
	 * @param string $oldValue - старое короткое название стандартного значения
	 * @param string $newValue - новое короткое название стандартного значения
	 * @return bool
	 */
	public function updateRelatedActivities($oldValue, $newValue)
	{
	    if ( ! $oldValue OR ! $newValue OR ! $this->name )
	    {// @todo понять нужно ли здесь выбрасывать исключение вместо return false
	        return false;
	    }
	    // получаем все связанные записи со старым значением
	    $criteria = new CDbCriteria();
	    $criteria->compare('type', $this->name);
	    $criteria->compare('value', $oldValue);
	    
	    $activities = QActivity::model()->findAll($criteria);
	    
	    foreach ($activities as $activity)
	    {// заменяем его на новое
	        $activity->value = $newValue;
	        $activity->save();
	    }
	    
	    return true;
	}
	
	/**
	 * Определить, есть ли в базе анкеты, использующие это стандартное значение
	 * @return bool
	 */
	public function hasRelatedActivities()
	{
	    if ( $this->isNewRecord )
	    {
	        return false;
	    }
	    
	    $criteria = new CDbCriteria();
	    $criteria->compare('type', $this->name);
	    $criteria->compare('value', $this->value);
	    
	    return QActivity::model()->exists($criteria);
	}
	
	/**
	 * Проверить, уникально ли короткое название значения в пределах одного типа
	 * @param string $value - короткое название для стандартного значения
	 * @param bool $excludeSelf - не считать собственное значение (для обновления записи, если оно не изменилось)
	 * @return bool
	 */
	public function isUniqueShortName($value, $name=null, $excludeSelf=false)
	{
	    if ( ! trim($name) )
	    {// тип значения обязательно должен присутствовать
	        if ( ! $this->name )
	        {
	            return false;
	        }
	        $name = $this->name;
	    }
	    if ( ! trim($value) OR $value === 'custom' )
	    {// само значение тоже должно быть не пустым (слово "custom" - служебное)
	        return false;
	    }
	    
	    $criteria = new CDbCriteria();
	    $criteria->compare('name', $name);
	    $criteria->compare('value', $value);
	    
	    if ( $excludeSelf )
	    {
	        if ( ! $this->id )
	        {
	            return false;
	        }
	        $criteria->compare('id', '<>'.$this->id);
	    }
	    
	    return ! (bool)QActivityType::model()->exists($condition, $params);
	}
	
	/**
	 * Получить список всех анкет, в которых используется этот вариант значения
	 * @param string $id
	 * @return Questionary[]|null - анкеты
	 */
	public function getRelatedQuestionaries($id=null)
	{
	    
	}
}
