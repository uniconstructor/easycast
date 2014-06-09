<?php

/**
 * История создания анкет администраторами
 * Таблица "{{q_creation_history}}".
 *
 * The followings are the available columns in table '{{q_creation_history}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $questionaryid
 * @property string $timecreated
 */
class QCreationHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QCreationHistory the static model class
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
		return '{{q_creation_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objectid, questionaryid, timecreated', 'length', 'max' => 11),
			array('objecttype', 'length', 'max' => 50),
			array('objecttype', 'filter', 'filter' => 'trim'),
		    
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, objectid, questionaryid, timecreated', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    'user'        => array(self::BELONGS_TO, 'User', 'objectid'),
		);
	}
	
    /**
	 * Получить все использованные для создания анкеты источники данных (роль, пользователь)
	 * (именованая группа условий)
	 * 
	 * @param int $id
	 * @return QCreationHistory
	 */
	public function forQuestionary($id)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('questionaryid', $id);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все анкеты, которые были созданы через форму регистрации на роль
	 * @param int $vacancyId
	 * @return QCreationHistory
	 */
	public function forVacancy($vacancyId)
	{
	    return $this->forObject('vacancy', $vacancyId);
	}
	
	/**
	 * Именованая группа условий: получить все записи о созданных анкетах, привязанных к определенному объекту
	 * (например к роли)
	 * @param string $objectType - тип объекта (vacancy, user, ...)
	 * @param int $objectId - id объекта
	 * @return QCreationHistory
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->compare('objectid', $objectId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Именованая группа условий
	 * @param string $objectType - тип объекта (vacancy, user, ...)
	 * @return QCreationHistory
	 */
	public function forType($objectType)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	     
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
	        ),
	    );
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// перед добавлением новой записи проверим что такого значения еще нет
	        $criteria = new CDbCriteria();
	        $criteria->compare('objecttype', $this->objecttype);
	        $criteria->compare('objectid', $this->objectid);
	        $criteria->compare('questionaryid', $this->questionaryid);
	        
	        if ( $this->exists($criteria) )
	        {// одна анкета может быть введена админом не более одного раза
	            return false;
	        }
	    }
	    return parent::beforeSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'            => 'ID',
			'objectid'      => 'Objectid',
			'questionaryid' => 'Questionaryid',
			'timecreated'   => 'Timecreated',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('questionaryid', $this->questionaryid, true);
		$criteria->compare('timecreated', $this->timecreated, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}