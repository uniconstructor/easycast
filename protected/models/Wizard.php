<?php

/**
 * Модель для работы с разбитыми на этапы формами
 *
 * Таблица '{{wizards}}':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $filltype - тип заполнения формы - возможные значения:
 *                              'form' - заполнение в один клик (традиционная форма)
 *                              'wizard' - заполнение в несколько шагов (иногда с промежуточным сохранением)
 * @property string $timecreated
 * @property string $timemodified
 * @property string $objecttype
 * @property string $objectid
 */
class Wizard extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{wizards}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('timecreated, timemodified, objectid', 'length', 'max' => 11),
			array('objecttype', 'length', 'max' => 50),
			array('filltype', 'length', 'max' => 20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, timecreated, timemodified, objecttype, objectid', 'safe', 'on' => 'search'),
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
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
	    if ( $this->isNewRecord )
	    {// каждой новой форме при создании мы добавляем список для полей формы,
	        // на случай если эта форма будет не пошаговой а обычной
	        // создаем пустой список для полей формы
	        $easyList = new EasyList();
	        // в списке полей все элементы должны быть уникальными:
	        // нельзя добавить одно поле в форму два раза
	        $easyList->unique = 1;
	        $easyList->name   = 'Общий список полей формы ('.$this->name.')';
	        if ( ! $easyList->save() )
	        {
	            throw new CException('Не удалось создать новый список полей для формы регистрации');
	        }
	        
	        // прикрепляем список полей к шагу регистрации
	        $easyListInstance = new EasyListInstance();
	        $easyListInstance->easylistid = $easyList->id;
	        $easyListInstance->objecttype = 'Wizard';
	        $easyListInstance->objectid   = $this->id;
	        if ( ! $easyListInstance->save() )
	        {// что-то не так с привязкой списка - откатываем изменения
	            $easyList->delete();
	            throw new CException('Не удалось привязать созданный список полей к форме регистрации');
	        }
	    }
	    parent::afterSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => Yii::t('coreMessages', 'title'),
			'description' => Yii::t('coreMessages', 'description'),
			'filltype' => 'Тип заполнения',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
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
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('timemodified', $this->timemodified, true);
		$criteria->compare('objecttype', $this->objecttype, true);
		$criteria->compare('objectid', $this->objectid, true);
		$criteria->compare('filltype', $this->filltype, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Wizard the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий: получить все записи, связанные с определенным объектом
	 * @param string $objectType
	 * @param int    $objectId
	 * @return Wizard
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
}
