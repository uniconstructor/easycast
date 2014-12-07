<?php

/**
 * Списки для группировки объектов системы.
 * Один список может быть прикреплен одновременно к нескольким объектам
 * Списки могут содержать в себе модели разных типов, для этого каждую модель нужно прикрепить
 * к EasyListItem, и уже список EasyListItem можно перебирать как оычные связанные со списком записи
 * 
 * Списки могут быть:
 * - статическими (например снимок одобренных заявок на роль в определенный период)
 *   такие списки не пополняюстя и не очищаются со временем: данные в них всегда находятся в таком же
 *   состоянии как и в момент создания списка
 * - динамическими или дополняемыми: например список подходящих актеров на роль
 *   Если набор на роль идет несколько дней и на сайте регистрируются подходящие на
 *   роль актеры - то динамический список список будет пополнятся в зависимости от критериев поиска,
 *   которые прикреплены к нему
 *   
 * Каждый список может содержать только уникальные элементы 
 * (в такие списки нельзя добавить один и тот же объект два раза)
 * или же, наоборот, не требовать уникальности 
 * (один элемент можно добавлять в список много раз)
 * 
 * Если модель удаляется из списка - то мы обновляем статус EasyListItem и
 * устанавливаем objectid=0 но не удаляем саму запись элемента списка из базы чтобы сохранить историю
 *
 * Таблица '{{easy_lists}}':
 * 
 * @property integer $id
 * @property string $name           - название списка (необязательно)
 * @property string $description    - описание списка (необязательно)
 * @property string $triggerupdate  - тип событий, которые запускают процесс дополнения списка
 *                                    новыми записями (EasyListItem) в зависимости критериев поиска
 *                                    (never/manual/auto/all - см. описания констант этого класса)
 * @property string $triggercleanup - тип событий, которые запускают процесс очистки списка 
 *                                    от устаревших значений
 *                                    (never/manual/auto/all - см. описания констант этого класса)
 * @property string $timecreated
 * @property string $timemodified
 * @property string $lastupdate    - время последнего обновления списка (unix timestamp)
 *                                   Содержит "0" если список еще ни разу не обновлялся
 * @property string $lastcleanup   - время последней очистки списка (unix timestamp)
 *                                   Содержит "0" если список еще ни разу не был очищен
 * @property string $updateperiod  - интервал (в секундах) через который содержимое списка снова будет
 *                                   считаться устаревшим и требовать проверки
 * @property string $cleanupperiod - интервал (в секундах) через который содержимое списка снова будет
 *                                   считаться устаревшим и требовать проверки
 * @property string $unique        - должны ли элементы (EasyListItem) в этом списке быть уникальными?
 *                                   (не применимо для $this->objecttype='item' && $this->objectid=0)
 * @property string $itemtype      - тип элементов в списке (по умолчанию EasyListItem)
 *                                   если список содержит разородные элементы то в этом 
 *                                   поле будет значение mixed
 * @property string $searchdataid  - id набора поисковых критериев (SearchData), по которым определяется 
 *                                   какие элементы должны быть в списке а какие нет
 *                                   Используется при обновлении и очистке списка, статические списки не
 *                                   могут иметь поисковых критериев 
 * 
 * Relations:
 * @property EasyListInstance[] $instances - все экземпляры этого списка
 *           (если он прикреплен к другим объектам через objecttype/objectid)
 * @property EasyListItem[]     $listItems - все элементы входящие в этот спискок
 * @property SearchData         $searchData - условия выборки для элементов списка
 *           
 * Методы класса EcTimestampBehavior:
 * @method EasyList createdBefore(int $time, string $operation='AND')
 * @method EasyList createdAfter(int $time, string $operation='AND')
 * @method EasyList updatedBefore(int $time, string $operation='AND')
 * @method EasyList updatedAfter(int $time, string $operation='AND')
 * @method EasyList modifiedOnly()
 * @method EasyList neverModified()
 * @method EasyList lastCreated()
 * @method EasyList firstCreated()
 * @method EasyList lastModified()
 * @method EasyList firstModified()
 */
class EasyList extends CActiveRecord
{
    /**
     * @var string - условие запуска дополнения или очистки списка: запретить менять содержимое списка  
     *               (для статических списков которые работают как снимки состояния системы)
     *               Такие списки могут содержать или не содержать условия поиска, но никогда
     *               не могут быть обновлены автоматически или вручную.
     *               Обновлять эти списки запрещено обновлять потому что их главной задачей
     *               является сохранение точного состояния системы на определенный момент времени.
     *               Если такой список содержит условия поиска - то это поможет 
     *               сравнивать текущие результаты выборки с прошлыми.
     *               Если криериев поиска в таком списке нет - значит он или нужен для того
     *               чтобы сохранить хорошую подборку или для того чтобы запомнить связанных
     *               определенным событием или местом людей (например нам нужно точно запомнить
     *               сколько человек приехали вовремя, кто опоздал и конечно же, кто именно эти люди) 
     */
    const TRIGGER_NEVER  = 'never';
    /**
     * @var string - условие запуска дополнения или очистки списка: обновлять список только вручную
     *               (для динамических списков которые дополняются и очищаются вручную, без
     *               каких-либо правил или условий выборки)
     *               Такие списки не могут обновляться автоматически потому что не содержат критериев
     *               поиска для составления выборки записей
     *               Их удобно использовать для редко произвольно составляемых списков записей:
     *               например если нужно составить подборку из разных анкет участников для того чтобы
     *               показать их режиссеру как кандидатов на роль
     */
    const TRIGGER_MANUAL = 'manual';
    /**
     * @var string - условие запуска дополнения или очистки списка: обновлять список только автоматически
     *               (для динамических списков которые дополняются и очищаются по мере того
     *               как объекты системы начинают или перестают подходить условиям выборки)
     *               Такие списки будут обновляться автоматически (например по крону). 
     *               Период обновления должен быть больше нуля. 
     *               Обновлять такие списки вручную можно только если для них полностью 
     *               запрещена очистка неподходящих значений
     *               (потому что вручную добавленные объекты могут не подходить
     *               по критериям выборки списка, и первая же операция очистки удалит всех кто
     *               не прошел по параметрам)
     */
    const TRIGGER_AUTO   = 'auto';
    /**
     * @var string - условие запуска дополнения или очистки списка: обновлять список и автоматически и вручную
     *               (для динамических списков которые дополняются и очищаются по мере того
     *               как объекты системы начинают или перестают подходить условиям выборки)
     *               Такие списки будут обновляться по крону, период обновления может быть
     *               задан больше нуля (иначе будет доступно только ручное обновление списка).
     *               Такие списки содержат и критерии поиска и список вручную добавленных участников
     *               (как отдельный критерий "дополнительный список участников для поиска")
     *               Автоматически в этот список добавляются только подходящие по критериям поиска значения, 
     *               но мы можем вручную добавить к ним любые записи (при наличии прав, конечно же).
     *               Запуск очистки устаревших записей такого списка не удалит вручную введенные записи, 
     *               потому что присутствие в списке "участники, приглашенные на роль вручную" - 
     *               это такой же критерий поиска как рост или возраст
     *               (только он добавляется к остальным всегда через условие OR чтобы все критерии поиска
     *               и мой отобранный список имели одинаковую значимость)
     */
    const TRIGGER_ALL    = 'all';
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{easy_lists}}';
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	public function beforeDelete()
	{
	    if ( $this->isUsedByAnyObject() )
	    {// нельзя удалять списки на которые ссылаются другие модели системы
	        return false;
	    }
	    foreach ( $this->instances as $instance )
	    {
	        $instance->delete();
	    }
	    return parent::beforeDelete();
	} 

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
            array('unique', 'numerical', 'integerOnly' => true),
			array('name', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('triggerupdate, triggercleanup', 'length', 'max' => 20),
			array('itemtype', 'length', 'max' => 50),
			array('timecreated, timemodified, lastupdate, lastcleanup, updateperiod, unique, 
			    searchdataid', 'length', 'max' => 11),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
		    // связи этого списка с другими объектами
		    // (если он прикреплен к ним через пару objecttype/objectid)
		    'instances' => array(self::HAS_MANY, 'EasyListInstance', 'easylistid'),
		    // все элементы входящие в этот спискок
		    'listItems' => array(self::HAS_MANY, 'EasyListItem', 'easylistid'),
		    // условия выборки для элементов списка
		    'searchData' => array(self::BELONGS_TO, 'SearchData', 'searchdataid')
		);
		// подключаем связи для настроек
		if ( ! $this->asa('ConfigurableRecordBehavior') )
		{
		    $this->attachBehavior('ConfigurableRecordBehavior', array(
		        'class' => 'application.behaviors.ConfigurableRecordBehavior',
		        'defaultOwnerClass' => get_class($this),
		    ));
		}
		$configRelations = $this->asa('ConfigurableRecordBehavior')->getDefaultConfigRelations();
		return CMap::mergeArray($relations, $configRelations);
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
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationTargetBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
	            //'customRelations' => array(),
	        ),
	        // настройки для модели и методы для поиска по этим настройкам
	        'ConfigurableRecordBehavior' => array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
	            'defaultOwnerClass' => get_class($this),
	        ),
	    );
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
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
			'triggerupdate' => 'Дополннять список',
			'triggercleanup' => 'Очищать список',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'lastupdate' => 'Последний запуск дополнения списка',
			'lastcleanup' => 'Последний запуск очистки списка',
			'updateperiod' => 'Интервал дополнения списка',
			'cleanupperiod' => 'Интервал очистки списка',
			'unique' => 'Запретить одинаковые элементы в этом списке?',
			'itemtype' => 'Тип элементов списка',
			'searchdataid' => 'Условия выборки для элементов списка',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * 
	 * @param  string $className active record class name.
	 * @return EasyList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованная группа условий поиска: Получить все списки содержащие элемент 
	 * с указанным id, либо ссылающиеся на него
	 * 
	 * @param  EasyListItem|int|array $item - id или модель значения (EasyListItem) или массив id
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return EasyList
	 */
	public function forItem($item, $operation='AND')
	{
        if ( $item instanceof EasyListItem )
	    {
	        $itemId = $item->id;
	    }else
	    {
	        $itemId = $item;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'listItems' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes' => array(
    	            'withItemId' => array($itemId),
    	        ),
	        ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * (alias) Именованная группа условий поиска: Получить все списки содержащие элемент
	 * с указанным id, либо ссылающиеся на него
	 *
	 * @param  EasyListItem|int|array $itemId - id или модель значения (EasyListItem) или массив id
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return EasyList
	 */
	public function withItem($item, $operation='AND')
	{
	    return $this->forItem($item, $operation);
	}
	
	/**
	 * Именованная группа условий: получить все списки содержащие элемент с указанным значением
	 * или значением соответствующим хотя бы одному из элементов переданного массива
	 *
	 * @param  string|array $value - значение или список значений которые ищутся в поле value
	 * @param  string $operation   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return EasyList
	 */
	public function withItemValue($value, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'listItems' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes' => array(
    	            'withItemValue' => array($value),
    	        ),
	        ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * Получить варианты обновления содержимого списка
	 * 
	 * @return array
	 * 
	 * @todo перенести в системные настройки (системные списки)
	 */
	public function getTriggerOptionsList()
	{
	    return array(
	        self::TRIGGER_NEVER  => 'Никогда',
	        self::TRIGGER_MANUAL => 'Только вручную',
	        self::TRIGGER_AUTO   => 'Только автоматически',
	        self::TRIGGER_ALL    => 'Любым способом',
	    );
	}
	
	/**
	 * Определить, используется ли этот список хотя бы одним объектом системы
	 * 
	 * @return bool
	 */
	public function isUsedByAnyObject()
	{
	    // использование в настройках
	    if ( Config::model()->forDefaultList($this->id)->forUserList($this->id, 'OR')->exists() )
	    {// в качестве списка значений
	        return true;
	    }
	    if ( Config::model()->withValueType('EasyList')->withValueId($this->id)->exists() )
	    {// в качестве самого значения
	        return true;
	    }
	    // использование в фильтрах поиска
	    if ( SearchFilterField::model()->withDefaultListId($this->id)->exists() )
	    {
	        return true;
	    }
	    // на этот список не ссылается ни одна из моделей системы
	    return false;
	}
	
	/**
	 * @todo Определить, используется ли этот список указанным объектом системы
	 * 
	 * @param  string $objectType - модель, использующая это значения
	 * @param  number $objectId - id модели: если не передан - то ищем во всех моделях указанного типа
	 * @return bool
	 */
	/*public function isUsedBy($objectType, $objectId=0)
	{
	    
	}*/
	
	/**
	 * Определить, содержится ли указанный элемент (или ссылка на него) в этом списке 
	 *
	 * @param  int|array|EasyListItem $item - модель или id элемента или массив id если элементов несколько
	 * @param  bool $searchLinks - искать ли ссылки на указанный элемент помимо самого элемента? 
	 * @return bool
	 */
	public function hasItem($item, $searchLinks=true)
	{
	    if ( $item instanceof EasyListItem )
	    {
	        $itemId = $item->id;
	    }else
	    {
	        $itemId = $item;
	    }
	    if ( EasyListItem::model()->forList($this->id)->withItemId($itemId, $searchLinks)->exists() )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, содержит ли список элемент с переданным значением
	 *
	 * @param  string|array $value - значение или список значений которые ищутся в поле value
	 * @return bool
	 */
	public function hasItemValue($value)
	{
	    return EasyListItem::model()->forList($this->id)->withItemValue($value)->exists();
	}
	
	/**
	 * Получить элемент списка по его значению: ищет в списке элемент с таким id,
	 * если не находит - то ищет ссылку на него
	 * Возвращает false если в этом списке нет ни такого элемента ни ссылки на него
	 *
	 * @param  string $value
	 * @return EasyListItem
	 */
	public function getItemWithValue($value)
	{
	    if ( ! $item = EasyListItem::model()->forList($this->id)->withItemValue($value)->find() )
	    {
	        return false;
	    }
	    if ( $item AND $item->easylistid == $this->id )
	    {// элемент присутствует в списке
	        return $item;
	    }
	    return false;
	}
	
	/**
	 * Получить элемент списка: ищет в списке элемент с таким id, 
	 * если не находит - то ищет ссылку на него 
	 * Возвращает false если в этом списке нет ни такого элемента ни ссылки на него
	 * 
	 * @param  int|EasyListItem $item - элемент списка (оригинал или ссылка на него)
	 * @return EasyListItem
	 */
	public function getItem($itemId)
	{
	    if ( $itemId instanceof EasyListItem )
	    {
	        $item = $itemId;
	    }else
	    {
	        $item = EasyListItem::model()->forList($this->id)->withItemId($itemId)->find();
	    }
	    if ( $item AND $item->easylistid == $this->id )
	    {// элемент присутствует в списке
	        return $item;
	    }
	    // в этом списке нет ни такого элемента ни ссылки на него
	    return false;
	}
	
	/**
	 * Добавить элемент в этот список: этот метод копирует данные 
	 * переданного элемента списка, не изменяя ее значений
	 * 
	 * @param  EasyListItem $item - новый элемент списка
	 * @return bool|EasyListItem
	 */
	public function addItem(EasyListItem $item)
	{
	    if ( $this->hasItem($item) )
	    {// элемент или ссылка на него уже присутствует в списке - дальнейшие действия не требуются 
	        return $this->getItem($item);
	    }
	    // получаем данные элемента
	    $attributes = $item->attributes;
	    // убираем лишние поля 
	    unset($attributes['id']);
	    unset($attributes['timecreated']);
	    unset($attributes['timemodefied']);
	    unset($attributes['sortorder']);
	    
	    // привязываем новый элемент к этому списку
	    $attributes['easylistid'] = $this->id;
	    if ( ! $attributes['parentid'] )
	    {// запоминаем ссылку на оригинальный элемент (если добавляемый элемент являлся оригиналом значения)
	        $attributes['parentid'] = $item->id;
	    }
	    // создаем и сохраняем новый элемент списка
	    $newItem = new EasyListItem();
	    $newItem->attributes = $attributes;
	    if ( ! $newItem->save() )
	    {// @todo add to log
	        return false;
	    }
	    return $newItem;
	}
	
	/**
	 * Удалить элемент из списка вспомогательный метод, чтобы было проще работать с элементами списка
	 * Если элемента с переданным id нет в списке - то ищет и удаляет из списка ссылку на него 
	 * 
	 * @param  int|EasyListItem $item - 
	 * @return bool
	 */
	public function removeItem($item)
	{
	    if ( $item instanceof EasyListItem )
	    {
	        $itemId = $item->id;
	    }else
	    {
	        $itemId = $item;
	    }
	    if ( $currentItem = $this->getItem($itemId) )
	    {// удаляем элемент из списка, гарантированно не затрагивая при этом оригинал
	        return $currentItem->delete();
	    }
	    // элемент уже удален
	    return true;
	}
	
	/**
	 * Создать копию этого списка и перенести в нее все элементы
	 * 
	 * @return EasyList
	 */
	public function createCopy()
	{
	    // получаем данные списка
	    $fields = array('name', 'description', 'triggerupdate', 'triggercleanup', 'updateperiod',
	       'cleanupperiod', 'unique', 'itemtype', 'searchdataid');
	    // создаем новый список из данных старого
	    $newList = new EasyList();
	    $newList->attributes = $this->getAttributes($fields);
	    if ( ! $newList->save() )
	    {// @todo add to log
	        return false;
	    }
	    foreach ( $this->listItems as $item )
	    {// копируем все элементы в новый список 
	        if ( ! $newList->addItem($item) )
	        {// не удалось скопировать элементы в новый список - отменяем операцию
	            // @todo add to log
	            $newList->delete();
	            return false;
	        }
	    }
	    return $newList;
	}
}