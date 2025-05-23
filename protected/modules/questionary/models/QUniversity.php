<?php

/**
 * Модель для хранения музыкального или театрального ВУЗа
 * 
 * @todo проставить property-поля для работы code competetion
 * @todo удалить поле system
 */
class QUniversity extends CActiveRecord
{
    /**
     * @var string - тип ВУЗа: театральный
     */
    const TYPE_THEATRE = 'theatre';
    /**
     * @var string - тип ВУЗа: музыкальный
     */
    const TYPE_MUSIC   = 'music';
    
    /**
     * 
     * @param system $className
     * @return CActiveRecord
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
		return '{{q_universities}}';
	}

	/**
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('type', 'length', 'max' => 16),
			array('type', 'required'),
			array('name', 'length', 'max' => 128),
			array('name', 'required'),
			array('link', 'length', 'max' => 255),
            array('system, form, search, timecreated, timemodified', 'numerical', 'integerOnly'=>true),
			array('id, type, name, link, system', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return array(
		    
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
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    if ( $this->hasRelatedInstances() )
	    {// запрещаем удалять ВУз если он используется хотя бы в одной анкете
	        return false;
	    }
	    return parent::beforeDelete();
	}

	/**
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => Yii::t('coreMessages', 'type'),
			'name' => Yii::t('coreMessages', 'name'),
			'link' => Yii::t('app', 'Link'),
			'system' => 'Отображать в меню?',
			'form' => 'Предлагать в форме поиска?',
			'search' => 'Предлагать при заполнении анкеты?',
		);
	}

	/**
	 * 
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('link',$this->link,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

    /**
     * Проверить, есть ли в нашей базе ВУЗ с таким названием
     * @param string $name - название ВУЗа
     *
     * @return bool|int - id найденного ВУЗа (если он существует) или false
     *                    если в нашей базе нет ВУЗа с таким названием
     */
    public function universityExists($name)
    {
        $id = intval($name);
        if ( is_numeric($id) AND $this->exists("id = :id", array(':id' => $id)) )
        {// есть ВУЗ с таким id
            return $id;
        }elseif ( $universities = $this->findAll('name = :name', array(':name' => $name)) )
        {// есть ВУЗ с таким названием
            $university = current($universities);
            return $university->id;
        }
        
        // такого ВУЗа еще нет в нашей базе
        return 0;
    }

    /**
     * Добавить к нашему списку ВУЗов новый (введенный пользователем)
     * @param string $name - название ВУЗа
     * @param string $type - тип ВУЗа - музыкальный (music) или театральный (theatre)
     * @param int $system - делать ли значение стандартным? (1 или 0)
     *                       Стандартные значения добавляются только админами из редактора
     *                       Все ВУЗы добавленные пользователями изначально не считаются стандартными
     *
     * @return int - id новой записи
     */
    public function addUserUniversity($name, $type, $system=0)
    {
        $university = new QUniversity;
        $university->system = $system;
        $university->type   = $type;
        $university->link   = null;
        $university->name   = strip_tags($name);

        $university->save();

        return $university->id;
    }
    
    /**
     * Заменить во всех анкетах один ВУЗ на другой
     * (используется редактором стандартных значений при редактировании списка ВУЗов)
     * @param int $oldId - id ВУЗа который будет убран из всех анкет 
     * @param int $newId - id ВУЗа который будет установлен вместо удаленного
     * @param string $type - тип ВУЗа
     *                 misic - музыкальный
     *                 theatre - театральный
     * @return bool
     */
    public function updateRelatedInstances($oldId, $newId)
    {
        Yii::import('application.modules.questionary.models.QUniversityInstance');
        
        if ( ! QUniversity::model()->exists('`id` =  :id', array(':id' => $oldId)) )
        {// старый ВУЗ не существует
            throw new CDbException('Old university not found. id='.$oldId);
        }
        if ( ! QUniversity::model()->exists('`id` = :id', array(':id' => $newId)) )
        {// Новый ВУЗ не существует
            throw new CDbException('New university not found. id='.$newId);
        }
        
        if ( ! $instances = QUniversityInstance::model()->findAll('`universityid` = :oldid', array(':oldid' => $oldId)) )
        {// нет записей со старым ВУЗом - значит нечего обновлять
            return true;
        }
        
        foreach ( $instances as $instance )
        {
            $instance->universityid = $newId;
            $instance->save();
        }
        
        return true;
    }
    
    /**
     * Определить, еслть ли анкеты, в которых используется этот ВУЗ
     * @return boolean
     */
    public function hasRelatedInstances()
    {
        if ( $this->isNewRecord )
        {
            return false;
        }
        Yii::import('application.modules.questionary.models.QUniversityInstance');
        
        return QUniversityInstance::model()->findAll('`universityid` = :id', array(':id' => $this->id));
    }
}
