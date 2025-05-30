<?php

/**
 * Класс для работы с одним ВУЗом в котором учился пользователь
 * 
 * @todo прописать property-теги
 */
class QUniversityInstance extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('questionary.extensions.behaviors.QSaveYearBehavior');
        parent::init();
    }
    
    /**
     * @param system $className
     * @return QUniversityInstance
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
		return '{{q_university_instances}}';
	}

	/**
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('questionaryid, universityid, timestart, timeend, timecreated, timemodified', 
			    'length', 'max' => 11),
            // ВУЗ
            array('name, specialty', 'length', 'max' => 255),
		    array('name', 'filter', 'filter' => 'trim'),
            // год окончания
            array('year', 'numerical', 'integerOnly' => true),
		    array('year', 'filter', 'filter' => 'trim'),
		    array('year', 'required'),
            // Мастерская
            array('workshop', 'length', 'max' => 255),
		    array('workshop', 'filter', 'filter' => 'trim'),

			array('id, type, questionaryid, universityid, timestart, timeend, 
			    workshop, timecreated, timemodified', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    'university'  => array(self::BELONGS_TO, 'QUniversity', 'universityid'),
		);
	}

	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return array(
		    // сохранение поля "год"
            'QSaveYearBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QSaveYearBehavior',
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
            'name'         => QuestionaryModule::t('university_label'),
            'universityid' => QuestionaryModule::t('university_label'),
            'year'         => QuestionaryModule::t('finish_year'),
            'workshop'     => QuestionaryModule::t('workshop'),
            'specialty'    => QuestionaryModule::t('specialty'),
		);
	}

	/**
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('universityid',$this->universityid,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('workshop',$this->workshop,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

    /**
     * @see parent::beforeSave
     */
    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    /**
     * Получить название ВУЗа
     * @return mixed
     */
    public function getname()
    {
        if ( isset($this->university->name) AND $this->university->name )
        {
            return $this->university->name;
        }
        return '';
    }

    /**
     * Получить тип ВУЗа
     */
    public function gettype()
    {
        if ( isset($this->university->type) AND $this->university->type )
        {
            return $this->university->type;
        }
        return null;
    }

    /**
     * Установить ВУЗ введенный пользователем
     * @param string $name
     */
    public function setname($name)
    {
        if ( $this->universityid )
        {// ВУЗ выбран из выпадающего списка - нам передан id
            return;
        }
        if ( ! trim($name) )
        {// название пустое - не сохраняем его
            return;
        }
        if ( $id = QUniversity::model()->universityExists($name) )
        {// нашли ВУЗ по названию - запишем его id
            $this->universityid = $id;
            return;
        }
        // ВУЗ не выбран из списка и не найден по названию - добавим его в наш справочник
        $this->universityid = QUniversity::model()->addUserUniversity($name, $this->defaultType);
    }

    /**
     * @param int $id - id ВУЗа в справочнике
     */
    public function setuniversityid($id)
    {
        if ( QUniversity::model()->exists('id = :id', array(':id' => $id)) )
        {
            $this->universityid = $id;
        }
    }
    
    /**
     * Получить список ВУЗов для выпадающего меню (id => "Название ВУЗа")
     * 
     * @param string $type - тип списка вузов: "theatre" - театральные, "music" - музыкальные 
     * @return array 
     */
    public function getUniversityList($type)
    {
        $result   = array();
        $criteria = new CDbCriteria();
        $criteria->compare('type', $type);
        $criteria->order = 'name';
        
        if ( QuestionaryModule::SYSTEM_DEFAULTS_ONLY )
        {// разрешены только одобренные администратором ВУЗы
            $criteria->compare('system', 1);
        }
        if ( ! $universities = QUniversity::model()->findAll($criteria) )
        {// ни одного ВУЗа не найдено
            return array();
        }
        foreach ( $universities as $university )
        {
            $result[$university->id] = $university->name;
        }
        return $result;
    }
}
