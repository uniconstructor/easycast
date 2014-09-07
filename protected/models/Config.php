<?php

/**
 * Модель для работы с настройками приложения.
 * Настройки могут быть прикреплены к любой модели в системе
 *
 * Таблица '{{config}}':
 * 
 * @property int    $id
 * @property int    $easylistid   - id списка со стандартными значениями этой настройки 
 *                                  (для select-списков)
 *                                  0 если стандартных значений не предусмотрено
 * @property int    $parentid     - id базовой настройки. Базовой считается настройка, которая
 *                                  была использована как шаблон чтобы создать эту модель.
 *                                  Значения, заданные в родительской настройке считаются стандартными
 *                                  значениями для дочерних настроек
 *                                  Самым верхнем уровнем являются системные настройки: 
 *                                  они не привязываются к какому-либо объекту в системе, а служат
 *                                  образцом для создания настроек такого же типа.
 * @property string $name         - служебное имя настройки, маленькие латинские буквы и точки
 *                                  @todo две системные настройки не могут иметь одинаковых названий
 * @property string $title        - название настройки для отображения
 * @property string $description  - пояснение для настройки
 * @property string $type         - тип настройки (чаще всего совпадает с названием input-типа для
 *                                  элемента формы или названием класса виджета, который будет использован
 *                                  для вывода этой настройки)
 * @property int    $minvalues    - минимальное количество значений которые нужно выбрать в этой настройке
 *                                  0  - заполнение необязательно
 *                                  1  - заполнение обязательно
 *                                  >1 - все что больше единицы: требуется выбрать как минимум n значений
 *                                  иначе настройка не будет считаться заполненой. Заполнение обязательно.
 * @property int    $maxvalues    - максимальное количество выбранных значений для этой настройки
 *                                  0  - неограничено
 *                                  1  - только одно значение 
 *                                  (используется для текстовых строк, JSON-значений а также для обычных
 *                                  элементов radio или select)
 *                                  >1 - все что больше единицы: ограничение максимального количества
 *                                  одновременно выбранных вариантов
 * @property string $objecttype   - тип объекта к которому привязана настройка: чаще всего здесь
 *                                  указан класс модели к которой привязана эта настройка
 *                                  Для системных настроек это поле равно "system"
 * @property int    $objectid     - id объекта к которому привязана настройка
 * @property int    $timecreated  - дата создания
 * @property int    $timemodified - последнее изменение
 * 
 * Relations:
 * @property ConfigValue[] $configValues - все значения этой настройки
 * @property Config        $parentConfig - родительская настройка, из которой была создана эта
 * 
 * @todo проверка для максимального/минимального количества значений
 * @todo проверка правильности указания служебного имени
 */
class Config extends CActiveRecord
{
    const TYPE_TEXT         = 'text';
    const TYPE_TOGGLE       = 'toggle';
    const TYPE_RADIO        = 'radio';
    const TYPE_CHECKBOX     = 'checkbox';
    const TYPE_CHECKBOXLIST = 'checkboxlist';
    const TYPE_SELECT       = 'select';
    const TYPE_MULTISELECT  = 'multiselect';
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{config}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, title', 'required'),
			array('name, title', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('type', 'length', 'max' => 20),
			array('objecttype', 'length', 'max' => 50),
			array('easylistid, parentid, minvalues, maxvalues, objectid, timecreated, timemodified', 
			    'length', 
			    'max' => 11,
            ),
			
			// The following rule is used by search().
			array('id, easylistid, parentid, name, title, description, type, 
			    minvalues, maxvalues, objecttype, objectid, timecreated, timemodified', 
			    'safe', 
			    'on' => 'search',
            ),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        $listTypes = array(self::TYPE_CHECKBOXLIST, self::TYPE_SELECT, self::TYPE_RADIO);
	        if ( in_array($this->type, $listTypes) )
	        {// для новой настройки создаем список стандартных значений (если требуется)
                $easyList = new EasyList();
                $easyList->name = 'Список значений для настройки "'.$this->title.'"';
                $easyList->save();
                // привязываем созданный пустой список к этой настройке
                $this->easylistid = $easyList->id;
	        }
	    }
	    
	    if ( $this->easylistid AND ! EasyList::model()->findByPk($this->easylistid) )
	    {// задан id списка значений - нужно проверить что он не пустой
	        throw new CException('Невозможно сохранить настройку: указанный в ней список стандартных
	            значений (id='.$this->easylistid.') не существует');
	    }
	    if ( $this->parentid AND ! Config::model()->findByPk($this->parentid) )
	    {// задан id списка значений - нужно проверить что он не пустой
	        throw new CException('Невозможно сохранить настройку: указанная родительская настройка
	            (id='.$this->easylistid.') не существует');
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	public function beforeDelete()
	{
	    foreach ( $this->configValues as $value )
	    {
	        $value->delete();
	    }
	    return parent::beforeDelete();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // все значения этой настройки
		    'configValues' => array(self::HAS_MANY, 'ConfigValue', 'configid'),
		    // родительская настройка (из которой создана эта)
		    'parentConfig' => array(self::BELONGS_TO, 'Config', 'parentid'),
		    // список стандартных значений
		    'easyList' => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
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
			'easylistid' => 'Список значений для этой настройки',
			'parentid' => 'Шаблон для этой настройки',
			'name' => Yii::t('coreMessages', 'name'),
			'title' => Yii::t('coreMessages', 'title'),
			'description' => Yii::t('coreMessages', 'description'),
			'type' => 'Тип настройки',
			'minvalues' => 'Минимальное количество выбранных значений',
			'maxvalues' => 'Максимальное количество выбранных значений (включительно)',
			'timecreated' => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
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
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('easylistid',$this->easylistid,true);
		$criteria->compare('parentid',$this->parentid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('minvalues',$this->minvalues,true);
		$criteria->compare('maxvalues',$this->maxvalues,true);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Config the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованая группа условий: все записи привязанные к объекту определенного типа
	 *
	 * @param  string $objectType
	 * @param  int    $objectId
	 * @return Config
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все записи привязанные к нескольким объектам
	 *
	 * @param string $objectType
	 * @param array  $objectIds
	 * @return Config
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->addInCondition($this->getTableAlias(true).'.`objectid`', $objectIds);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
}