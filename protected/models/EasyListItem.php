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
 * @property string $sortorder - поля для сортировки записей: уникально в рамках привязки к списку (easylistid)
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
 * @property int $parentid - id оригинального элемента:  
 *                           используется только для ссылки на модели EasyListItem
 *                           (этот элемент является ссылкой на другой элемент из другого списка)
 * 
 * Relations:
 * @property EasyList       $easyList      - список которому принадлежит значение
 * @property CActiveRecord  $valueObject   - модель на которую ссылается этот элемент списка
 * @property EasyListItem[] $itemInstances - все элементы списка, ссылающиеся на эту модель любым способом
 * @property EasyListItem   $parentItem    - элемент списка из которого был создан этот элемент
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
 * @todo виртуальное поле data (getter, read-only + labels)
 * @todo добавить sortable-поведение и миграцию, исправляющую порядок сортировки для всей таблицы
 * @todo добавить константы для остальных используемых objecttype
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
     * @var string - тип элемента списка: простое значение
     */
    const TYPE_ITEM       = '__item__';
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{easy_list_items}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 * 
	 * @todo проверки уникальности и существования связанных записей
	 */
	public function rules()
	{
		return array(
            array('easylistid, objectid, sortorder, timecreated, timemodified, parentid', 
                'length', 'max' => 11,
            ),
            array('status, objecttype, objectfield', 'length', 'max' => 50),
            array('name', 'length', 'max' => 255),
            array('description, value, data', 'length', 'max' => 4095),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 * 
	 * @todo добавить возможность требовать уникальность не только по objecttype/objectid но и по value
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
	        if ( $this->easyList AND $this->easyList->unique AND ! $this->isOriginalItem() AND 
	             $this->parentItem->easylistid == $this->easyList->id )
	        {// разрешены только уникальные элементы внутри списка
	            // (в этот список нельзя добавить один и тот же элемент элемент 2 раза)
	            // исключение составляют только элементы с типом 'item' и objectid=0: 
	            // они не ссылаются на другие объекты в базе и мы не можем проверить их уникальность:
	            // для этого проверяем, присутствует ли добавляемый элемент элемент в этом списке
	            if ( $this->forList($this->easylistid)->forObject($this->objecttype, $this->objectid)->exists() )
	            {// и отменяем вставку новой записи если такой элемент уже есть
	                $msg = "Не удалось добавить запись: такой элемент уже есть в списке (objectid=".$this->objectid.')';
	                Yii::log($msg, CLogger::LEVEL_INFO, 'application.easylistitem');
                    return false;
	            }
	        }
	        // для ссылок на поле в другой модели: при создании новой записи копируем значение
	        // из связанного поля, чтобы ускорить поиск по нему  и уменьшить количество JOIN
	        // при выборке, особенно при поиске разнородных объектов
	        if ( ! $this->isOriginalItem() AND $this->getTargetObject() AND $this->objectfield )
	        {
	            if ( ! $this->getTargetObject()->hasRelated($this->objectfield) )
	            {// поля-связи могут содержать большие выборки, поэтому их мы не кешируем
	                $this->value = $this->valueObject->{$this->objectfield};
	            }
	        }
	        // @todo пока в эту модель не добавлен workflow-плагин будем ставить активный статус руками 
	        $this->status = self::STATUS_ACTIVE;
	    }else
	    {// обновляется существующая запись
	        $old = $this::model()->findByPk($this->id);
            $new = $this;
            foreach ( $this->itemInstances as $item )
            {// обновим сохраненные данные в ссылках на эту запись
                if ( $fieldName = $item->objectfield )
                {// определем на какое поле в качестве оригинала ссылкается элемент
                    if ( $old->$fieldName != $new->$fieldName )
                    {
                        $item->value = $new->$fieldName;
                        $item->save();
                    }
                }elseif ( $old->$fieldName != $new->$fieldName )
                {// если элемент ссылается на модель целиком - то только синхронизируем даты изменения
                    $item->markModified();
                }
            }
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
	    parent::afterSave();
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	public function beforeDelete()
	{
	    if ( $this->isUsedByAnyObject() )
	    {// запрещаем удалять элементы списка, которые используются другими моделями системы
	        // (например в качестве значения настройки) чтобы не создавать битых ссылок
	        $msg = "Не удалось удалить элемент списка: он используется 
	            другими объектами системы (id=".$this->id.")";
	        Yii::log($msg, CLogger::LEVEL_INFO, 'application.easylistitem');
	        return false;
	    }
	    return parent::beforeDelete();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
		    // список в которой находится значение
		    'easyList'      => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
		    // оригинал значения (только для элементов-ссылок)
		    'parentItem'    => array(self::BELONGS_TO, 'EasyListItem', 'parentid'),
		    // все элементы списка, ссылающиеся на эту модель любым способом
		    'itemInstances' => array(self::HAS_MANY, 'EasyListItem', 'parentid'),
		    // список в котором находится оригинал значения элемента (только для элементов-ссылок)
		    'parentList'    => array(self::BELONGS_TO, 'EasyList', 'easylistid', 'through' => 'parentItem'),
		    // @todo все списки в которых присутствует это значение (через "through")
		);
		return $relations;
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
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // поведение для связи с другими моделями
	        'CustomRelationSourceBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationSourceBehavior',
	            'targetRelationName'  => 'valueObject',
	            'customObjectTypes'   => array(self::TYPE_ITEM),
	            'enableEmptyObjectId' => true,
	        ),
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationTargetBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
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
		    'name'         => Yii::t('coreMessages', 'system_name'),
		    'title'        => Yii::t('coreMessages', 'title'),
		    'data'         => Yii::t('coreMessages', 'content'),
		    'value'        => Yii::t('coreMessages', 'value'),
		    'description'  => Yii::t('coreMessages', 'description'),
			'timecreated'  => Yii::t('coreMessages', 'timecreated'),
			'timemodified' => Yii::t('coreMessages', 'timemodified'),
		    'sortorder'    => Yii::t('coreMessages', 'sortorder'),
			'status'       => Yii::t('coreMessages', 'status'),
			'parentid'     => 'Оригинал элемента списка',
		);
	}
	
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
	        //'order'     => $this->getTableAlias(true, false).'.`sortorder` ASC',
	        // по умолчанию скрываем из любой выборки удаленные элементы
	        'condition' => $this->getTableAlias(true, false).".`status` <> '".self::STATUS_DELETED."'",
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
            // все элементы у которых не указано значение в поле "objectfield"
            // (это записи которые ссылаются на объект целиком, а не на поле в модели)
            'withEmptyObjectField' => array(
	            'condition' => $this->getTableAlias(true).'.`objectfield` IS NULL OR '.
                    $this->getTableAlias(true).".`objectfield` = '' ",
	        ),
	    );
        return CMap::mergeArray($timestampScopes, $modelScopes);
	}
	
	/**
	 * (alias) Именованная группа условий: получить все элементы из указанного списка 
	 * Найдет все элементы присутствующие хотя бы в одном из списков если передан массив
	 * 
	 * @param  int|array|EasyListItem $list - id списка, модель списка (EasyList) или массив id списков
	 * @param  string $operation            - как присоединить это условие к остальным?
	 *                                        (AND/OR/AND NOT/OR NOT)
	 * @return EasyListItem
	 */
	public function forList($list, $operation='AND')
	{
	    if ( $list instanceof EasyList )
	    {
	        $listId = $list->id;
	    }else
	    {
	        $listId = $list;
	    }
	    return $this->withListId($listId, $operation);
	}
	
	/**
	 * (alias) Именованная группа условий: получить все элементы из указанного списка
	 * Найдет все элементы присутствующие хотя бы в одном из списков если передан массив
	 *
	 * @param  int|array|EasyListItem $list - id списка, модель списка (EasyList) или массив id списков
	 * @param  string $operation            - как присоединить это условие к остальным?
	 *                                        (AND/OR/AND NOT/OR NOT)
	 * @return EasyListItem
	 */
	public function inList($list, $operation='AND')
	{
	    return $this->forList($listId, $operation);
	}
	
	/**
	 * (alias) Именованная группа условий: все элементы, кроме тех которые присутствуют в указанном списке
	 * Если передан массив - то будут найдены все элементы, кроме тех которые присутствуют
	 * хотя бы в одном из переданных списков
	 *
	 * @param  int|array|EasyListItem $list - id списка, модель списка (EasyList) или массив id списков
	 * @param  string $operation            - как присоединить это условие к остальным?
	 *                                        (AND/OR/AND NOT/OR NOT)
	 * @return EasyListItem
	 */
	public function notInList($list, $operation='AND')
	{
	    if ( $list instanceof EasyList )
	    {
	        $listId = $list->id;
	    }else
	    {
	        $listId = $list;
	    }
	    return $this->exceptListId($listId, $operation);
	}
	
	/**
	 * Именованная группа условий: получить все элементы из указанного списка
	 * 
	 * @param  int|array $listId - id списка (EasyListItem) или массив id списков
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return EasyListItem
	 */
	public function withListId($listId, $operation='AND')
	{
	    return $this->withCustomValue('easylistid', $listId, $operation);
	}
	
	/**
	 * Именованная группа условий: все элементы, кроме тех которые присутствуют в указанном списке
	 * Если передан массив - то будут найдены все элементы, кроме тех которые присутствуют
	 * хотя бы в одном из переданных списков
	 * 
	 * @param  int|array $listId - id списка (EasyList) или массив id списков
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return EasyListItem
	 */
	public function exceptListId($listId, $operation='AND')
	{
	    return $this->exceptCustomValue('easylistid', $listId, $operation);
	}
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле objectfield
	 * 
	 * @param  string|array $objectField - значение или список значений которые ищутся в поле objectfield
	 * @param  string       $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return EasyListItem
	 */
	public function withObjectField($objectField, $operation='AND')
	{
	    if ( ! $objectField )
	    {// условие не используется
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objectfield`', $objectField);
	     
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * Группа условий поиска: выбрать записи с указанным статусом
	 * 
	 * @param  array|string $status - массив статусов или строка если статус один
	 * @return EasyListItem
	 * 
	 * @todo использовать одноименный метод SWActiveRecordBehavior после подключения simpleWorkflow
	 */
	public function withStatus($status, $operation='AND')
	{
	    if ( ! $status )
	    {// Если статус не указан - выборка по этому параметру не требуется
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`status`', $status);
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * (alias) Именованная группа условий: получить все элементы c указаным значением в поле value
	 * или значением соответствующим хотя бы одному из значений если пердан массив
	 * (поскольку мы сохраняем все внешние значения полей в записи элемента списка
	 * нам нет необходимости составлять сложный JOIN-запрос по связаным таблицам:
	 * мы просто ищем по полю value внутри таблицы {{easy_list_items}})
	 * 
	 * @param  string|array $value
	 * @return EasyListItem
	 */
	public function withValue($value, $operation='AND')
	{
	    return $this->withItemValue($value, $operation);
	}
	
	/**
	 * Именованная группа условий: получить все элементы c указаным значением в поле value
	 * или значением соответствующим хотя бы одному из вариантов если передан массив
	 * 
	 * @param string|array $value - значение или список значений которые ищутся в поле value
	 * @param bool $includeLinked - также найти все элементы которые все элементы которые
	 *                              содержат указанное значение в связанном объекте
	 * @return EasyListItem
	 */
	public function withItemValue($value, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`value`', $value);
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * Условие поиска: получить все элементы списка с указанным id либо ссылающиеся на элемент c указанным id
	 *
	 * @param  int|array|EasyListItem $item - id элемента списка (EasyListItem)
	 * @param  bool $withLinks              - включить в выборку не только элементы с указанным id,
	 *                                        но и ссылки на них 
	 * @return EasyListItem
	 */
	public function withItemId($item, $withLinks=true, $operator='AND')
	{
	    if ( $item instanceof EasyListItem )
	    {
	        $itemId = $item->id;
	    }else
	    {
	        $itemId = $item;
	    }
	    // ищем элементы с указанными id
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`id`', $itemId);
	    if ( $withLinks )
	    {// ищем копии этих элементов (они ссылаются на указанные через поле parentid)
	        $parentCriteria = new CDbCriteria();
	        $parentCriteria->compare($this->getTableAlias(true).'.`parentid`', $itemId);
	        $criteria->mergeWith($parentCriteria, 'OR');
	    }
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	    
	    return $this;
	}
	
	/**
	 * Условие поиска: 
	 *
	 * @param  int|array|EasyListItem $item - id элемента списка (EasyListItem)
	 * @param  bool $withLinks              - учитывать в запросе не только элементы с указанным id
	 *                                        но и ссылки на них
	 * @return EasyListItem
	 */
	public function exceptItemId($item, $withLinks=true, $operator='AND')
	{
	    if ( $item instanceof EasyListItem )
	    {
	        $itemId = $item->id;
	    }else
	    {
	        $itemId = $item;
	    }
	    if ( ! is_array($itemId) )
	    {
	        $itemId = array($itemId);
	    }
	    // ищем элементы с указанными id
	    $criteria = new CDbCriteria();
	    $criteria->addNotInCondition($this->getTableAlias(true).'.`id`', $itemId);
	    if ( $withLinks )
	    {// ищем копии этих элементов (они ссылаются на указанные через поле parentid)
	        $criteria->addNotInCondition($this->getTableAlias(true).'.`parentid`', $itemId);
	    }
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	     
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все элементы 
	 * c указаным значением в поле связанного объекта
	 * (или значением соответствующим хотя бы одному из значений если передан массив)
	 * 
	 * @param  string $objectType - 
	 * @param  string $objectField - 
	 * @param  string|array $value - значение или список значений которые ищутся в связанном объекте
	 * @return EasyListItem
	 * 
	 * @todo непонятно как применять такое условие поиска к моделям в общем виде:
	 *       переписать этот метод используя objecttype и objectfield заданные извне
	 */
	public function withLinkedObjectValue($objectType, $objectField, $value, $operator='AND')
	{
	    throw new CException('NOT IMPLEMENED');
	    
	    /*$model = $this->objecttype;
	    $alias = $model::model()->getTableAlias(true);
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
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	     
	    return $this;*/
	}
	
	/**
	 * Все элементы с указанным значением в поле parentid 
	 * 
	 * @param  int|array|EasyListItem $parent   - 
	 * @param  string                 $operator - 
	 * @return EasyListItem
	 */
	public function withParentId($parent, $operator='AND')
	{
	    if ( $parent instanceof EasyListItem )
	    {
	        $parentId = $parent->id;
	    }else
	    {
	        $parentId = $parent;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`parentid`', $parentId);
	     
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	    
	    return $this;
	}
	
	/**
	 * Все элементы, кроме тех у которых переданное значение в parentid 
	 * Если указан массив id - то найдутся все записи, кроме тех которые содержат
	 * хотя бы одно из перечисленных id в поле $parentId
	 * 
	 * @param  int|array|EasyListItem $parent   - 
	 * @param  string                 $operator - 
	 * @return EasyListItem
	 */
	public function exceptParentId($parent, $operator='AND')
	{
	    if ( $parent instanceof EasyListItem )
	    {
	        $parentId = $parent->id;
	    }else
	    {
	        $parentId = $parent;
	    }
	    if ( ! is_array($parentId) )
	    {
	        $parentId = array($parentId);
	    }
	    $criteria = new CDbCriteria();
	    $criteria->addNotInCondition($this->getTableAlias(true).'.`parentid`', $parentId);
	     
	    $this->getDbCriteria()->mergeWith($criteria, $operator);
	    
	    return $this;
	}
	
	/**
	 * Получить значение, которое содержится в этом элементе списка
	 * 
	 * @return string|CActiveRecord
	 */
	public function getData()
	{
	    if ( $this->isOriginalItem() )
	    {//элемент содержит оригинал значения, на него сылаются элементы в других списках
	        return $this->value;
	    }
	    if ( $this->parentItem )
	    {// элемент содержит ссылку на оригинал - берем данные из него
	        return $this->parentItem->getData();
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
	 * @param  string $newData
	 * @return bool
	 * 
	 * @todo предусмотреть вариант с передачей модели
	 */
	public function setData($newData, $saveNow=true)
	{
	    $this->value = $newData;
	    if ( $saveNow )
	    {
	        return $this->save();
	    }
	    return true;
	}
	
	/**
	 * Геттер для названия элемента
	 * 
	 * @return string
	 */
	public function getTitle()
	{
	    if ( $this->name )
	    {// если есть название элемента - сначала берем его: 
	        return $this->name;
	    }
	    if ( $this->parentItem )
	    {
	        $this->parentItem->title;
	    }
	    return $this->value;
	}
	
	/**
	 * Сеттер для названия элемента
	 * 
	 * @param  string $title
	 * @return void
	 */
	public function setTitle($title)
	{
	    $this->name = $title;
	}
	
	/**
	 * Определить является ли этот элемент списка "оригиналом" - то есть той записью, на которую
	 * ссылаются все остальные элементы при дублировании списка
	 * 
	 * @return bool
	 */
	public function isOriginalItem()
	{
	    if ( $this->objecttype === self::TYPE_ITEM AND ! $this->parentid )
	    {// элементы со служебным типом всегда являются оригиналом
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
	    if ( $this->objecttype === self::TYPE_ITEM )
	    {// этот элемент списка - просто ссылка на другой такой же элемент
	        return true;
	    }
	    if ( $this->isOriginalItem() )
	    {// элемент списка содержит простое значение и не ссылается на другие модели
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, используется ли этот список хотя бы одним объектом системы
	 *
	 * @return bool
	 */
	public function isUsedByAnyObject()
	{
	    // использование в настройках
	    if ( Config::model()->withValueType('EasyListItem')->withValueId($this->id)->exists() )
	    {// в качестве одиночного значения
	        return true;
	    }
	    /*if ( Config::model()->withValueType('EasyList')->withValueId($this->easylistid)->
	         withSelectedOption($this->id)->exists() )
	    {// в качестве одного из нескольких выбранных значений
            return true;
	    }*/
	    // использование другими элементами списка
	    if ( $this->itemInstances )
	    {// в качестве оригинала значения
	        return true;
	    }
	    // на этот список не ссылается ни одна из моделей системы
	    return false;
	}
	
	/**
	 * Получить привязанный к этому элементу списка объект
	 * 
	 * @return CActiveRecord
	 * 
	 * @deprecated использовать $this->getTargetObject(), удалить при рефакторингге
	 */
	/*public function getProxy()
	{
	    return $this->getTargetObject();
	}*/
	
	/**
	 * Для элементов-ссылок: обновить сохраненное значение из внешней таблицы
	 * 
	 * @return bool - было ли обновлено значение
	 */
	public function updateCachedValue()
	{
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
	    // определяем из какого поля модели брать значение
	    if ( ! $fieldName = $this->objectfield )
	    {// для элементов, которые не ссылаются на конкретное поле: если целевой объект был изменен
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
	
	/**
	 * добавить элемент в список
	 * 
	 * @param  int|EasyList $list - 
	 * @return EasyListItem
	 */
	public function copyToList($list)
	{
	    if ( ! is_object($list) )
	    {
	        if ( ! $list = EasyList::model()->findByPk($list) )
	        {// ненайден список с таким id
	            throw new CException('Не найден список с таким id (id='.$list.')');
	        }
	    }
	    return $list->addItem($this);
	}
	
	/**
	 * @todo переместить элемент в список
	 * 
	 * @param  int|EasyList $list
	 * @return EasyListItem
	 */
	public function moveToList($list)
	{
	    
	}
}