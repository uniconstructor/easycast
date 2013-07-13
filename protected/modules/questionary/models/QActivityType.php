<?php

/**
 * Тип деятельности.
 * Используется для составления списков стандартных значений при заполнении анкеты.
 *
 * Хранит все возможные стандартные типы умений, навыков и характеристик (типы вокала, музыкальные инструменты,
 * стили танца, и т. п.).
 * Добавленные пользователем собственные (нестандартные) значения не хранятся здесь - они находятся в QActivity
 *
 * @todo возможно в будущем следует реализовать админский интерфейс, в котором можно будет
 *       добавлять дополнительные типы деятельности из пользовательских.
 *       (когда пользовательских наберется достаточно много)
 *       при этом у пользовательского значения (QActivity) слово custom в поле value,
 *       и заменяется на введенное админом короткое обозначение.
 *       После этого по базе ищутся все использования этого значения и заменяются на стандартные
 */
class QActivityType extends CActiveRecord
{
    /**
     * @return QActivityType|CActiveRecord
     */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return '{{q_activity_types}}';
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('name', 'length', 'max'=>20),
			array('name', 'required'),
			array('value', 'length', 'max'=>32),
		    array('value', 'required'),
			array('translation', 'length', 'max'=>255),
		    array('translation', 'required'),
			array('id, name, value', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return array('CAdvancedArBehavior',
				array('class' => 'ext.CAdvancedArBehavior')
				);
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'name' => 'Тип значения',
			'value' => 'Короткое название. Без пробелов и кавычек, только латинскими буквами, лучше всего перевод на английский. Например: "Горные лыжи" => "ski". Если слов несколько то их нужно разделять_подчеркиванием.',
			'translation' => 'Текст (отображается пользователю)',
		);
	}

	/**
	 * 
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('name',$this->name,true);

		$criteria->compare('value',$this->value,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        if ( ! $this->isUniqueShortName($this->value) )
	        {// короткое название не уникально - так не должно быть
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
	        {// короткое название значения изменилось - обновим все связяные записи
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
	 * (non-PHPdoc)
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    // После добавления нового стандартного значения проверим, было ли оно введено пользователем раньше
	    // и если да - то обновим анкеты таких пользователей
	    
	    if ( $this->isNewRecord )
	    {// обновляем существующие анкеты только если новое стандартное значение добавляется
	        $criteria = new CDbCriteria();
	        $criteria->condition = "`type` = :type AND `value` = 'custom' AND `uservalue` = :uservalue";
	        $criteria->params = array(
	            ':type'      => $this->name,
	            ':uservalue' => $this->translation,
	        );
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
	 * (non-PHPdoc)
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
	 * Получить список вариантов для одного вида деятельности (для использования в select-элементах)
	 * @param string $name - тип деятельности (вид спорта, вокал, и т. п.)
	 */
	public function activityVariants($name)
	{
	    $result = array();
        	    
	    $criteria = new CDbCriteria();
	    $criteria->addCondition('name = :name');
	    $criteria->params = array(':name' => $name);
	    $criteria->order  = '`translation` ASC';
	    
	    if ( ! $variants = $this->findAll($criteria) )
	    {
	        return array();
	    }
	
	    foreach ( $variants as $variant )
	    {
	        $result[$variant->value] = CHtml::encode($variant->translation);
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
	    {
	        return false;
	    }
	    
	    Yii::import('application.modules.questionary.models.QActivity');
	    
	    // получаем все связанные записи со старым значением
	    $criteria = new CDbCriteria();
	    $criteria->condition = "`type` = :type AND `value` = :value";
	    $criteria->params = array(':type' => $this->name, ':value' => $oldValue);
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
	 * @return boolean
	 */
	public function hasRelatedActivities()
	{
	    if ( $this->isNewRecord )
	    {
	        return false;
	    }
	    Yii::import('application.modules.questionary.models.QActivity');
	    
	    $criteria = new CDbCriteria();
	    $criteria->condition = "`type` = :type AND `value` = :value";
	    $criteria->params = array(':type' => $this->name, ':value' => $this->value);
	    
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
	    
	    if ( ! trim($value) or $value=='custom' )
	    {// само значение тоже должно быть не пустым
	        // слово "custom" - служебное
	        return false;
	    }
	    
	    $params = array(
	        ':name' => $name,
	        ':value' => $value,
	    );
	    
	    if ( $excludeSelf )
	    {
	        if ( ! $this->id )
	        {
	            return false;
	        }
	        $params[':id'] = $this->id;
	        $condition = "`name` = :name AND `value` = :value AND `id` <> :id";
	    }else
       {
            $condition = "`name` = :name AND `value` = :value";
	    }
	    
	    return ! QActivityType::model()->exists($condition, $params);
	}
}
