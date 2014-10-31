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
 * Такие элементы списков должны иметь значения objecttype='EasyListItem' и objectid=собственный_id
 * (для упрощения поиска по связанным объектам)
 * 
 * Извлекаемые записи всегда сортируются по sortorder если не указано иное
 *
 * Таблица '{{easy_list_items}}':
 * 
 * @property integer $id
 * @property string $easylistid  - id списка которому принадлежит значение
 * @property string $objecttype  - тип объекта который добавлен в список при помощи этой модели
 * @property string $objectfield - поле модели в котором хранится значение элемента
 *                                 (для случаев когда элемент списка является ссылкой 
 *                                 на значение поля в другой модели)
 * @property string $objectid    - id объекта который добавлен в список при помощи этой модели
 *                                 Для упрощения поиска по связанным объектам 
 * @property string $name - отображаемое название элемента списка (если связанный объект не имеет имени)
 *                          В разных списках одни и те же объекты могут называться по-разному,
 *                          это полезно для случаем когда название элемента из списка зависит от контекста
 *                          Помогает избежать лишних запросов к смежным при получении списков имен объектов
 *                          Если objecttype имеет значение 'EasyListItem', а objectid совпадает с
 *                          собственым id записи - то такой элемент списка не ссылается 
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
 * @property string|CActiveRecord $data - псевдо-поле (геттер) для получения содержимого из элемента
 *                                        списка: возвращает строку для элементов хранящих оригиналы
 *                                        значений или ссылки на значения полей в других таблицах
 *                                        Возвращает связанную модель для элементов хранящих ссылки на
 *                                        модель целиком
 * 
 * Relations:
 * @property EasyList      $easyList    - список которому принадлежит значение
 * @property CActiveRecord $valueObject - модель на которую ссылается этот элемент списка
 * 
 * Методы класса EcTimestampBehavior:
 * @method CActiveRecord createdBefore(int $time, string $operation='AND')
 * @method CActiveRecord createdAfter(int $time, string $operation='AND')
 * @method CActiveRecord updatedBefore(int $time, string $operation='AND')
 * @method CActiveRecord updatedAfter(int $time, string $operation='AND')
 * @method CActiveRecord modifiedOnly()
 * @method CActiveRecord neverModified()
 * @method CActiveRecord lastCreated()
 * @method CActiveRecord firstCreated()
 * @method CActiveRecord lastModified()
 * @method CActiveRecord firstModified()
 * 
 * @todo внедрить workflow-патерн (плагин SimpleWorkflow): это сильно упростит работу по синхронизации
 * @todo фальшивое поле data (read-only, labels)
 */
class EasyListItem extends CActiveRecord
{
    /**
     * @var string - статус элемента: возможный элемент списка (соответствует 'pending' для mailChip)
     */
    const STATUS_DRAFT    = 'draft';
    /**
     * @var string - статус элемента: текущий элемент списка (соответствует 'subscribed' для mailChip)
     */
    const STATUS_ACTIVE   = 'active';
    /**
     * @var string - статус элемента: бывший элемент списка (соответствует 'unsubscribed' для mailChip)
     */
    const STATUS_FINISHED = 'finished';
    /**
     * @var string - статус элемента: удаленный элемент списка (соответствует 'cleaned' для mailChip)
     */
    const STATUS_DELETED  = 'deleted';
    
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
			array('name', 'length', 'max' => 255),
			array('description, value, data', 'length', 'max' => 4095),
			// The following rule is used by search().
			//array('id, easylistid, objecttype, objectid, name, sortorder, timecreated, timemodified, status', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 * @todo добавить возможность требовать уникальность не только по objecttype/objectid
	 *       но и по value
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// автоматически назначаем порядок сортировки для новых записей, добавляя их в конец списка
	        // @todo вынести в behavior
	        $lastItemCount = $this->forList($this->easylistid)->count();
	        $lastItemCount++;
	        $this->sortorder = $lastItemCount;
	        
	        // проверяем уникальность нового элемента в списке (если этого требует)
	        if ( $this->easyList AND $this->easyList->unique AND ! $this->isOriginalItem() )
	        {// разрешены только уникальные элементы внутри списка
	            // (в этот список нельзя добавить один и тот же элемент элемент 2 раза)
	            // исключение составляют только элементы с типом 'item' и objectid=0: 
	            // они не ссылаются на другие объекты в базе и мы не можем проверить их уникальность:
	            // для этого проверяем, присутствует ли добавляемый элемент элемент в этом списке
	            $existedItem = $this->forList($this->easylistid)->forObject($this->objecttype, $this->objectid)->exists();
	            if ( $existedItem )
	            {// и отменяем вставку новой записи если такой элемент уже есть
                    return false;
	            }
	        }
	        
	        // для ссылок на поле в другой модели: при создании новой записи копируем значение
	        // из связанного поля, чтобы ускорить поиск по нему  и уменьшить количество JOIN
	        // при выборке, особенно при поиске разнородных объектов
	        if ( ! $this->isOriginalItem() AND $this->getTargetObject() AND $this->objectfield )
	        {
	            if ( ! $this->getTargetObject()->hasRelated($this->objectfield) )
	            {// поля-связи мы не кешируем
	                $objectField = $this->objectfield;
	                $this->value = $this->valueObject->$objectField;
	            }
	        }
	        // @todo пока в эту модель не добавлен workflow-плагин то будем ставить активный статус руками
	        $this->status = 'active';
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
	    if ( $this->objecttype === 'item' AND ! $this->objectid )
	    {// оригинал записи после сохранения делаем ссылкой на самого себя
	        $this->objecttype  = 'EasyListItem';
	        $this->objectfield = 'value';
	        $this->objectid    = $this->id;
	        $this->save(false, array('objecttype', 'objectfield', 'objectid'));
	    }
	    parent::afterSave();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
	    $objectType = 'EasyListItem';
        if ( isset($this->objecttype) AND $objectType = $this->objecttype )
        {
            $objectType = $this->objecttype;
        }
		return array(
		    // список в которой находится значение
		    'easyList'    => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
		    // модель, на которую ссылается этот элемент списка
		    //'valueObject' => array(self::BELONGS_TO, $objectType, 'objectid'),
		    // все элементы списка, ссылающиеся на значение поля 'value' из этой модели
		    'itemValueInstances' => array(self::HAS_MANY, 'EasyListItem', 'objectid',
		        'scopes' => array(
    		        'forObjectType'   => array('EasyListItem'),
		            'withObjectField' => array('value'),
    		    ),
            ),
		    // все элементы списка, ссылающиеся на эту модель любым способом
		    'itemInstances' => array(self::HAS_MANY, 'EasyListItems', 'objectid',
		        'scopes' => array(
    		        'forObjectType' => array('EasyListItem'),
    		    ),
            ),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'EcTimestampBehavior' => array(
	            'class' => 'application.behaviors.EcTimestampBehavior',
	        ),
	        // это поведение позволяет изменять набор связей модели в процессе выборки
	        'CustomScopesBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // поведение для связи с другими моделями
	        'CustomRelationSourceBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationSourceBehavior',
	            'targetRelationName'  => 'valueObject',
	            'customObjectTypes'   => array('system'),
	            'enableEmptyObjectId' => true,
	        ),
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationTargetBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
	            'customRelations' => array(),
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
		    //'name'         => 'Название элемента или связанного объекта внутри этого списка ',
		    'name'         => 'Заголовок',
		    'data'         => 'Содержимое',
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
	 * @see parent::model()
	 * 
	 * @param  string $className active record class name.
	 * @return EasyListItem|CustomRelationSourceBehavior|CustomRelationSourceBehavior|CustomScopesBehavior
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
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    // условия поиска по датам создания и изменения
	    $timestampScopes = $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	    // собственные условия поиска модели
        $modelScopes = array(
	        // все элементы привязанные к какой-либо модели
	        'linkedToObject' => array(
	           'condition' => $this->getTableAlias(true).'.`objectfield` IS NULL AND '.
	               $this->getTableAlias(true).'.`objectid` > 0',
            ),
	        // все элементы привязанные к значению поля какой-либо модели
	        // @todo проверить что objecttype не пустой и является классом модели
	        'linkedToObjectValue' => array(
	           'condition' => $this->getTableAlias(true).'.`objectfield` IS NOT NULL AND '.
	               $this->getTableAlias(true).'.`objectid` > 0',
            ),
	    );
        return CMap::mergeArray($timestampScopes, $modelScopes);
	}
	
	/**
	 * Именованная группа условий: получить все элементы из указанного списка (alias)
	 * 
	 * @param  int|array $listId - id списка (EasyListItem)
	 * @return EasyListItem
	 */
	public function forList($listId, $operation='AND')
	{
	    return $this->withListId($listId, $operation);
	}
	
	/**
	 * Именованная группа условий: получить все элементы из указанного списка
	 * 
	 * @param  int|array $listId - id списка (EasyListItem)
	 * @return EasyListItem
	 */
	public function withListId($listId, $operation='AND')
	{
	    return $this->withCustomValue('easylistid', $listId, $operation);
	}
	
	/**
	 * Именованая группа условий: все элементы всех списков, связанные с определенным объектом
	 * (например, если нужно узнать в каких списках числится объект)
	 * @param string $objectType
	 * @param int    $objectId
	 * @return EasyListItem
	 */
	/*public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}*/
	
	/**
	 * Именованая группа условий: то же что и forObject, но с одновременным поиском по нескольким
	 * объектом одного типа
	 * @param string $objectType
	 * @param array  $objectIds - массив id объектов
	 * @return EasyListItem
	 * 
	 * @deprecated использовать forObject($objectType, $objectId)
	 */
	/*public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->addInCondition($this->getTableAlias(true).'.`objectid`', $objectIds);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}*/
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле objecttype
	 * @param string $objectType - значение или список значений которые ищутся в поле objectfield
	 * @return EasyListItem
	 */
	/*public function withObjectType($objectType)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}*/
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле objectid
	 * (или значением соответствующим хотя бы одному из переданных значений если пердан массив)
	 * @param string|int $objectId - значение или список значений которые ищутся в поле objectid
	 * @return EasyListItem
	 */
	public function withObjectId($objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле objectfield
	 * @param string $objectField - значение или список значений которые ищутся в поле objectfield
	 * @return EasyListItem
	 */
	public function withObjectField($objectField)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objectfield`', $objectField);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все элементы у которых не указано значение в поле 
	 * "objectfield" (это записи которые ссылаются на объект целиком, а не на поле в нем)
	 * @param  string $objectField - значение или список значений которые ищутся в поле objectfield
	 * @return EasyListItem
	 */
	public function withEmptyObjectField()
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objectfield` IS NULL');
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - выбрать записи по статусам
	 * @param  array|string $statuses - массив статусов или строка если статус один
	 * @return EasyListItem
	 */
	public function withStatus($statuses)
	{
	    if ( empty($statuses) )
	    {// Если статус не указан - выборка по этому параметру не требуется
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`status`', $statuses);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле value
	 * или значением соответствующим хотя бы одному из значений если пердан массив
	 * (поскольку мы сохраняем все внешние значения полей в записи элемента списка
	 * нам нет необходимости составлять сложный JOIN-запрос по связаным таблицам:
	 * мы просто ищем по полю value внутри таблицы {{easy_list_items}})
	 * 
	 * @param  string|array $value
	 * @return EasyListItem
	 */
	public function withValue($value)
	{
	    return $this->withItemValue($value);
	}
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле value
	 * или значением соответствующим хотя бы одному из значений если передан массив
	 * 
	 * @param string|array $value - значение или список значений которые ищутся в поле value
	 * @param bool $includeLinked - также найти все элементы которые все элементы которые
	 *                              содержат указанное значение в связанном объекте
	 * @return EasyListItem
	 * 
	 * @todo предусмотреть возможность численного сравнения и поиска по LIKE-шаблону
	 *       для случая если передано одно значение (испольновать иногда compare())
	 */
	public function withItemValue($value)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`value`', $value);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи ссылающиеся на указаный элемент списка
	 * 
	 * @param  int|array|EasyListItem $item - id списка, ссылки на который нужно найти: если передан
	 *                                        массив id - то результат будет зависеть от второго параметра
	 * @return EasyListItem
	 */
	public function forItem($item)
	{
	    if ( ! $item )
	    {// условие не используется
	        return $this;
	    }
	    if ( is_array($item) OR is_numeric($item) )
	    {
	        $itemId = $item;
	    }elseif ( is_object($item) AND isset($item->id) )
	    {
	        $itemId = $item->id;
	    }else
	    {
	        throw new CException('Ошибочный формат данных при составлении условия поиска');
	    }
	    
	    return $this->forObject('EasyListItem', $itemId);
	}
	
	/**
	 * 
	 * @param  array  $items - массив id моделей элементов списка ссылки на которые 
	 *                         нужно найти. Результат поиска записит от переданного
	 *                         типа поиска (второй параметр)
	 * @param  string $operator - тип поиска: как составлять условие для списка элементов
	 *                            OR  - в списке должен быть хотя бы один элемент из массива
	 *                            AND - в списке должен быть каждый перечисленый элемент
	 * @param  bool $inverse - инвертировать полученное условие
	 * @return EasyListItem
	 * 
	 * @todo закончить работу над этой функцией
	 */
	/*public function forItems($items, $operator='OR', $inverse=false)
	{
	    if ( ! $items )
	    {// условие не используется
	        return $this;
	    }
	    if ( ! is_array($items) )
	    {
	        throw new CException('Для составления этого условия нужен массив');
	    }
	    $criteria = new CDbCriteria();
	    
	    // нормализуем формат массива
	    // @todo определять случай когда все объекты одного типа и сводить его к IN-условию
	    $itemIds = array();
	    $columns = array();
	    foreach ( $items as $item )
	    {
	        if ( isset($item->id) AND isset($item->objecttype) )
	        {// объекты для сравнения: для каждого объекта потребуется условие сравнения 
	            // по нескольким параметрам - дополняем список таких условий
	            $idPrefix = '';
	            if ( $inverse )
	            {// нужно исключитьиз выборки каждый id строго определенного типа
	                $idPrefix = '<>';
	            }
	            $columns[] = array(
	                'objecttype' => $item->objecttype,
	                'objectid'   => $idPrefix.$item->objectid,
	            );
	        }elseif ( is_numeric($item) )
	        {// id для сравнения: дополняем список для будущего IN-условия
	            $itemIds[] = $item;
	        }else
	        {
	            throw new CException('Для составления этого условия нужен массив из id или EasyListItem');
	        }
	    }
	    
	    // однотипные модели (например элементы списка) требуют более простых условий
	    if ( ! empty($itemIds) )
	    {// список id однотипных элементов списка (EasyListItem)
	        $idCriteria = new CDbCriteria();
	        $idCriteria->compare($this->getTableAlias(true).'.`objecttype`', 'EasyListItem');
	        if ( $inverse )
	        {// исключить элементы из выборки
	            if ( $operator == 'AND' )
	            {// исключить все записи
	                $idCriteria->addNotInCondition($this->getTableAlias(true).'.`objectid`', $itemIds);
	            }
	        }else
	        {
	            $idCriteria->addInCondition($this->getTableAlias(true).'.`objectid`', $itemIds);
	        }
            
	    }
	    
	    $columnCriteria = new CDbCriteria();
	    foreach ( $columns as $id => $columnData )
	    {// список разнородных элементов: для каждого из них нужно отдельное условие "тип + id"
	        $columnCriteria->addColumnCondition($columnData, 'AND', $operator);
	    }
	}*/
	
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
	    
	    // составляем условие поиска по произвольному полю
	    $fieldCriteria = new CDbCriteria();
	    $fieldCriteria->compare($alias.".`{$this->objectfield}`", $value);
	    
	    // составляем условие поиска по связаной модели
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'valueObject' => array(
	            'joinType'  => 'INNER JOIN',
    	        'condition' => $fieldCriteria->condition,
    	    ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Получить все элементы списка с указанным id либо ссылающиеся на этот элемент
	 * 
	 * @param int|array $itemId - id элемента списка (EasyListItem)
	 * @return EasyListItem
	 */
	public function withItemId($itemId, $includeLinked=true)
	{
	    if ( is_object($itemId) )
	    {// используем только id элемента
	        $itemId = $itemId->id;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`id`', $itemId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Обновить привязанный к элементу списка объект
	 * 
	 * @param  string $field
	 * @param  string $value
	 * @return bool
	 * 
	 * @todo добавлять возникшие при сохранении ошибки к ошибкам этой модели, в поле value
	 */
	public function updateProxy($field, $value)
	{
	    return $this->updateTargetObject($field, $value);
	}
	
	/**
	 * Получить значение, которое содержится в этом элементе списка
	 * @return string|CActiveRecord
	 */
	public function getData()
	{
	    if ( $this->isOriginalItem() )
	    {// частный случай ссылки для поля: элемент "содержащий сам себя" - он содержит оригинал
	        // значения на который сылаются записи в других списках
	        return $this->value;
	    }
	    if ( $fieldName = $this->objectfield )
	    {// ссылка на поле в другой модели
	        if ( $target = $this->getTargetObject() AND isset($target->$fieldName) )
	        {// проверяем что модель есть в базе и в ней есть нужное поле
	            return $target->$fieldName;
	        }else
	        {// модели с таким id нет в базе или в модели нет нужного поля
	            // @todo обработать эту ошибку
	        }
	    }else
	    {// ссылка на объект целиком
	        return $this->getTargetObject();
	    }
	}
	
	/**
	 * Сохранить значение элемента списка
	 * 
	 * @param string $newData
	 * @param bool   $updateExternal - изменять ли связанное значение если этот элемент списка
	 *                                 ссылается на другую модель?
	 * @return void
	 * 
	 * @todo предусмотреть вариант с передачей модели
	 */
	public function setData($newData, $updateExternal=false)
	{
	    $this->value = $newData;
	}
	
	/**
	 * Определить является ли этот элемент списка "оригиналом" - то есть той записью, на которую
	 * ссылаются все остальные элементы при дублировании списка
	 * 
	 * @return bool
	 */
	public function isOriginalItem()
	{
	    if ( $this->isNewRecord AND $this->objecttype === 'item' AND ! $this->objectid )
	    {// не сохраненные элементы всегда считаются уникальными
	        return true;
	    }
	    if ( $this->id == $this->objectid AND $this->objecttype === 'EasyListItem' )
	    {// ссылка на себя же
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить является ли этот элемент списка "простым" - то есть той записью, которая является
	 * "оригиналом" {@see EasyListItem::isOriginalItem()} либо ссылается только на одну запись строго 
	 * того же класса что и она сама (EasyListItem)
	 * 
	 * @return bool
	 */
	public function isSimpleItem()
	{
	    if ( $this->objecttype === 'EasyListItem' )
	    {
	        return true;
	    }
	    if ( $this->isOriginalItem() )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Получить привязанный к этому элементу списка объект
	 * @return CActiveRecord
	 * 
	 * @deprecated использовать $this->getTargetObject(), удалить при рефакторингге
	 */
	public function getProxy()
	{
	    return $this->getTargetObject();
	}
	
	/**
	 * Для элементов-ссылок: обновить сохраненное значение из внешней таблицы
	 * @return bool - было ли обновлено значение
	 */
	public function updateCachedValue()
	{
	    // определяем из какого поля модели брать значение
	    $fieldName = $this->objectfield;
	    
	    if ( $this->isOriginalItem() )
	    {// не обновляем те элементы которые ни на что не ссылаются
	        return true;
	    }
	    if ( ! $this->valueObject )
	    {// связанное значение было удалено - удаляем запись из списка 
	        // используем "мягкое удаление" (смену статуса), чтобы сохранить целостность связей БД 
	        // + сохраняем последнее значение поля перед удалением записи
	        // @todo применить workflow
	        // @todo переписать beforeDelete()
	        // @todo обработать возможные ошибки при смене статуса
	        $this->status = self::STATUS_DELETED;
	        return $this->save();
	    }
	    if ( in_array($this->valueObject->timemodified, array($this->timecreated, $this->timemodified)) )
	    {// связаный объект не редактировался за последнее время и содержит актуальное значение
	        // данные обновлять не нужно
	        return false;
	    }
	    if ( ! $fieldName )
	    {// для элемунтов, которые не ссылаются на конкретное поле: если целевой объект был изменен
	        // то достаточно взять из него время последнего изменения - остальное нас не интересует
	        $this->timemodified = $this->valueObject->timemodified;
	        return $this->save();
	    }
	    if ( $this->value != $this->valueObject->$fieldName )
	    {// значение связанного поля изменилось - обновим его локально 
	        $this->value = $this->valueObject->$fieldName;
	        return $this->save();
	    }
	    return false;
	}
	
	/**
	 * Вставить новую запись после указанной, пересчитав нумерацию
	 *
	 * @param  int $targetId
	 * @return bool
	 * 
	 * @todo вынести в поведение
	 */
	public function insertAfter($targetId)
	{
	    if ( ! $this->isNewRecord )
	    {
	        throw new CException('Not new record');
	    }
        if ( ! $targetModel = EasyListItem::model()->findByPk($targetId) )
        {
            throw new CException('Target id not found');
        }else
        {// добавляем значение в тот список в котором находится указанная модель
            $this->easylistid = $targetModel->easylistid;
        }
        
        // запоминаем освободившийся sortorder
        $this->sortorder = $targetModel->sortorder + 1;
        // сдвигаем все записи на 1 вперед 
	    $shiftedRecords = $this->forList($this->easylistid)->
            findAll($this->getTableAlias(true).".`sortorder` > {$targetModel->sortorder}");
	    
	    foreach ( $shiftedRecords as $record )
	    {
	        $record->sortorder += 1;
	        $record->save(false, array('sortorder'));
	    }
	    return $this->dbConnection->createCommand()->insert($this->tableName(), $this->attributes);
	}
}