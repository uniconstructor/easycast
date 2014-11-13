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
 * @property string $cleanUpPeriod - интервал (в секундах) через который содержимое списка снова будет
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
			array('timecreated, timemodified, lastupdate, lastcleanup, updateperiod, unique, searchdataid', 
			    'length', 'max' => 11),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // связи этого списка с другими объектами
		    // (если он прикреплен к ним через пару objecttype/objectid)
		    'instances' => array(self::HAS_MANY, 'EasyListInstance', 'easylistid'),
		    // все элементы входящие в этот спискок
		    'listItems' => array(self::HAS_MANY, 'EasyListItem', 'easylistid'),
		    // условия выборки для элементов списка
		    'searchData' => array(self::BELONGS_TO, 'SearchData', 'searchdataid')
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
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationTargetBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
	            'customRelations' => array(),
	        ),
	        // настройки для модели и методы для поиска по этим настройкам
	        'ConfigurableRecordBehavior' => array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
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
	 * @param  EasyListItem|int|array $itemId - id значения (EasyListItem) или массив id
	 * @return EasyList
	 */
	public function forItem($item)
	{
	    if ( is_object($item) )
	    {// вытаскиваем id из модели если передана модель
	        $item = $item->id;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'listItems' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes' => array(
    	            'withItemId' => array($item),
    	        ),
	        ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все списки содержащие элемент с указанным значением
	 * или значением соответствующим хотя бы одному из элементов переданного массива
	 *
	 * @param  string|array $value - значение или список значений которые ищутся в поле value
	 * @return EasyList
	 */
	public function withItemValue($value)
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
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
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
	 * 
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
}