<?php

/**
 * Элемент списка пользователей
 * Предполагается что одному элементу списка может принадлежать максимум одна модель  
 *
 * Таблица '{{user_list_items}}':
 * @property integer $id
 * @property string $easylistid
 * @property string $objecttype
 * @property string $objectid
 * @property string $name - отображаемое название элемента списка (если связанный объект не имеет имени)
 *                          В разных списках одни и те же объекты могут называться по-разному,
 *                          это полезно для случаем когда название элемента из списка зависит от контекста
 *                          Помогает избежать лишних запросов к смежным при получении списков имен объектов
 *                          Если objecttype имеет значение 'item' - то элемент списка не ссылается 
 *                          на другие объекты, а сам является возможным значением.
 *                          Поле name в этом случае используется как название элемента.
 *                          Тип 'item' используется для хранения введенных участником значений которые 
 *                          позже (после проверки) могут стать стандартными
 *                          (если в дополнении к стандартным пунктам списка разрешено ввести свой вариант), 
 * @property string $timecreated
 * @property string $timemodified
 * @property string $sortorder
 * @property string $status
 */
class EasyListItem extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{easy_list_items}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('status', 'required'),
			array('easylistid, objectid, sortorder, timecreated, timemodified', 'length', 'max'=>11),
			array('status, objecttype', 'length', 'max'=>50),
			array('name', 'length', 'max' => 255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, easylistid, objecttype, objectid, name, sortorder, timecreated, timemodified, status', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// автоматически назначаем порядок сортировки для новых записей, добавляя их в конец списка
	        // @todo вынести в behavior
	        $lastItemCount = $this->forList($this->easylistid)->count();
	        $lastItemCount++;
	        $this->sortorder = $lastItemCount;
	        // проверяем, присутствует ли уже этот элемент в этом списке
	        $existedItem = $this->forList($this->easylistid)->forObject($this->objecttype, $this->objectid)->exists();
	        if ( $existedItem )
	        {
	            return false;
	        }
	    }
	    return parent::beforeSave();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
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
			'easylistid' => 'Список',
			'objecttype' => 'Тип объекта',
			'objectid' => 'Номер объекта (id)',
		    'name' => 'Название объекта',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
		    'sortorder' => 'Порядок сортировки',
			'status' => 'Status',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('easylistid',$this->easylistid,true);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('sortorder',$this->sortorder,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserListItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => $this->getTableAlias(true, false).'.`sortorder` ASC'
	    );
	}
	
	/**
	 * Обновить привязанный к элементу списка объект
	 * @param string $field
	 * @param string $value
	 * @return bool
	 */
	public function updateProxy($field, $value)
	{
	    $proxy = $this->getProxy();
	    $proxy->$field = $value;
	    return $proxy->save();
	}
	
	/**
	 * Получить привязанный к этому элементу списка объект
	 * @return CActiveRecord
	 */
	public function getProxy()
	{
	    $modelClass = $this->objecttype;
	    return $modelClass::model()->findByPk($this->objectid);
	}
	
	/**
	 * Именованная группа условий: получить все элементы списка
	 * @param int $easyListId
	 * @return EasyListItem
	 */
	public function forList($easyListId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`easylistid`', $easyListId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все элементы всех списков, связанные с определенным объектом
	 * (например, если нужно узнать в каких списках числится объект)
	 * @param string $objectType
	 * @param int    $objectId
	 * @return EasyListItem
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
	 * Именованая группа условий: то же что и forObject, но с одновременным поиском по нескольким
	 * объектом одного типа
	 * @param string $objectType
	 * @param array  $objectIds - массив id объектов
	 * @return EasyListItem
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
