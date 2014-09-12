<?php

/**
 * Модель для одного элемента списка
 * Предполагается что одному элементу списка может принадлежать максимум одна модель
 * Так как разные элементы списка могут быть связаны с рзными соделями и при этом имеют общий
 * порядок сортировки в рамках одного списка - то это позволяет хранить в таких списках (EasyList)
 * модели принадлежащие разным AR-классам и использовать одно простое условие поиска 
 * для выборки разрородных объектов
 * 
 * Элемент списка также может не ссылаться на другие модели и вместо этого хранить данные
 * самостоятельно, что позволяет единообразным способом решить большинство промежуточных
 * задач по группировке/хранению даных не прибегая при этом к созданию дополнительных таблиц
 * и моделей с непонятным набором полей
 * Такие элементы списков должны иметь значения objecttype='item' и objectid=0
 *
 * Таблица '{{user_list_items}}':
 * 
 * @property integer $id
 * @property string $easylistid  - id списка которому принадлежит значение
 * @property string $objecttype  - тип объекта который добавлен в список при помощи этой модели
 * @property string $objectfield - поле модели в котором хранится значение элемента
 *                                 (для случаев когда элемент списка является ссылкой 
 *                                 на значение поля в другой модели)
 * @property string $objectid    - id объекта который добавлен в список при помощи этой модели
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
 * @property string $value - значение элемента списка (используется только если элемент списка не
 *                           привязан к модели или если значение из модели трудно достать,
 *                           или же получение нужного значения из связанной модели каждый раз
 *                           требует больших затрат ресурсов)
 * @property string $description - дополнительное описание для элемента списка 
 *                                (если потребуется хранить в списке элементы требующие подробного описания)
 * @property string $timecreated
 * @property string $timemodified
 * @property string $sortorder
 * @property string $status - статус присутствия записи в списке: список названий для статусов 
 *                            составлен по нашему стандарту кодирования (черновик всегда "draft",
 *                            основное рабочее состояние "active" и так далее)
 *                            Каждый статус нашего списка соответствует статусу таких же списов 
 *                            в mailChimp, чтобы нам было проще синхронизировать списки участников
 *                            для расылок по произвольным группам людей
 *                            Возможные значения статуса:
 *                            'draft'    - возможный элемент списка
 *                                         (соответствует 'pending' для mailChip)
 *                            'active'   - текущий элемент списка
 *                                         (соответствует 'subscribed' для mailChip)
 *                            'finished' - бывший элемент списка: может быть возвращен обратно в список
 *                                         (соответствует 'unsubscribed' для mailChip)
 *                            'deleted'  - удаленный элемент списка: не может быть возвращен обратно в список
 *                                         (соответствует 'cleaned' для mailChip)
 * 
 * Relations:
 * @property EasyList      $easyList - список которому принадлежит значение
 * @property CActiveRecord $valueObject - модель на которую ссылается этот элемент списка
 * 
 * @todo внедрить workflow-патерн (плагин SimpleWorkflow): это сильно упростит работу по синхронизации
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
			array('easylistid, objectid, sortorder, timecreated, timemodified', 'length', 'max' => 11),
			array('status, objecttype, objectfield', 'length', 'max' => 50),
			array('name, value', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			// The following rule is used by search().
			//array('id, easylistid, objecttype, objectid, name, sortorder, timecreated, timemodified, status', 'safe', 'on'=>'search'),
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
	        
	        if ( $this->easyList->unique AND $this->objecttype != 'item' )
	        {// разрешены только уникальные элементы внутри списка
	            // (в этот список нельзя добавить один и тот же элемент элемент 2 раза)
	            // исключение составляют только элементы с типом 'item' и objectid=0: 
	            // они не ссылаются на другие объекты в базе и мы не можем проверить их уникальность 
	            
	            // проверяем, присутствует ли добавляемый элемент элемент в этом списке
	            $existedItem = $this->forList($this->easylistid)->forObject($this->objecttype, $this->objectid)->exists();
	            if ( $existedItem )
	            {// отменяем вставку новой записи
	               return false;
	            }
	        }
	        // @todo пока в эту модель не добавлен workflow-плагин то будем ставить активный статус руками
	        $this->status = 'active';
	    }
	    return parent::beforeSave();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // список в которо находится значение
		    'easyList'    => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
		    // модель, на которую ссылается этот элемент списка
		    'valueObject' => array(self::BELONGS_TO, $this->objecttype, 'objectid'),
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
			'id'           => 'ID',
			'easylistid'   => 'Список',
			'objecttype'   => 'Тип объекта',
			'objectfield'  => 'Поле объекта',
			'objectid'     => 'id объекта',
		    'name'         => 'Название элемента или связанного объекта внутри этого списка ',
		    'value'        => 'Значение элемента списка',
		    'description'  => 'Описание для элемента списка или связанного объекта',
			'timecreated'  => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
		    'sortorder'    => 'Порядок сортировки',
			'status'       => Yii::t('coreMessages', 'status'),
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
	/*public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('easylistid', $this->easylistid,true);
		$criteria->compare('objecttype', $this->objecttype,true);
		$criteria->compare('objectid', $this->objectid,true);
		$criteria->compare('name', $this->name,true);
		$criteria->compare('timecreated', $this->timecreated,true);
		$criteria->compare('timemodified', $this->timemodified,true);
		$criteria->compare('sortorder', $this->sortorder,true);
		$criteria->compare('status', $this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}*/
	
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
	        // false вторым параметром нужен для предотвращения бесконечной рекурсии
	        'order' => $this->getTableAlias(true, false).'.`sortorder` ASC',
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
	 * Получить все элементы списка с указанным id либо ссылающиеся на этот элемент
	 * @param int|array $itemId - id элемента списка (EasyListItem)
	 * @return EasyListItem
	 */
	public function withItemId($itemId)
	{
	    if ( ! is_array($itemId) )
	    {
	        $itemId = array($itemId);
	    }
	    // условие для поиска элемента по id
	    $criteria = new CDbCriteria();
	    $criteria->addInCondition($this->getTableAlias(true).'.`id`', $itemId);
	    // условие для поиска ссылок на элемент
	    $linkCriteria = new CDbCriteria();
	    $linkCriteria->compare($this->getTableAlias(true).'.`objecttype`', 'EasyListItem');
	    $linkCriteria->addInCondition($this->getTableAlias(true).'.`objectid`', $itemId);
	    // нужны записи подходящие в любом из этих случаев
	    $criteria->mergeWith($linkCriteria, 'OR');
	    // совмещенное условие добавляем в итоговое
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
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле objectid
	 * (или значением соответствующим хотя бы одному из переданных значений если пердан массив)
	 * @param string|int $objectId - значение или список значений которые ищутся в поле objectid
	 * @return EasyListItem
	 */
	public function withObjectId($objectId)
	{
	    $criteria = new CDbCriteria();
	    if ( is_array($objectId) )
	    {
	        $criteria->addInCondition($this->getTableAlias(true).'.`objectid`', $objectId);
	    }else
	    {
	        $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	    }
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - выбрать записи по статусам
	 * @param  array|string $statuses - массив статусов или строка если статус один
	 * @return EasyListItem
	 */
	public function withStatus($statuses=array())
	{
	    $criteria = new CDbCriteria();
	    if ( ! is_array($statuses) )
	    {// нужен только один статус, и он передан строкой - сделаем из нее массив
	        $statuses = array($statuses);
	    }
	    if ( empty($statuses) )
	    {// Если статус не указан - выборка по этому параметру не требуется
	        return $this;
	    }
	    $criteria->addInCondition($this->getTableAlias(true).'.`status`', $statuses);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле value
	 * (или значением соответствующим хотя бы одному из значений если пердан массив)
	 * 
	 * @param string|array $value - значение или список значений которые ищутся в поле value
	 * @param bool $includeLinked - также найти все элементы которые все элементы которые
	 *                              содержат указанное значение в связанном объекте
	 * @return EasyListItem
	 */
	public function withValue($value, $includeLinked=false)
	{
	    $criteria = new CDbCriteria();
	    if ( is_array($value) )
	    {
	        $criteria->addInCondition($this->getTableAlias(true).'.`value`', $value);
	    }else
	    {
	        $criteria->compare($this->getTableAlias(true).'.`value`', $value);
	    }
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все элементы 
	 * c указаным значением в поле связанного объекта
	 * (или значением соответствующим хотя бы одному из значений если пердан массив)
	 * 
	 * @param string|array $value - значение или список значений которые ищутся в связанном объекте
	 * @return EasyListItem
	 * 
	 * @todo задать внтреннее условие через $criteria а не строкой 
	 *       чтобы можно было работать с массивом значений
	 * @todo стандартное условие withFieldValue в поведении для связанных объектов
	 */
	public function withLinkedValue($value)
	{
	    $model    = $this->objecttype;
	    $alias    = $model::model()->getTableAlias(true);
	    // составляем условие поиска по произвольному полю в связаной модели
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'valueObject' => array(
    	        'condition' => "{$alias}.`{$this->objectfield}` = '{$value}'"
    	    ),
	    );
	    $criteria->together = true;
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
}