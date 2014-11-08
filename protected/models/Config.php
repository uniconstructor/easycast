<?php

/**
 * Модель для работы с настройками приложения.
 * Настройки могут быть прикреплены к любой модели в системе. 
 * Значения настроек хранятся отдельно, в таблице config_values.
 * 
 * Чтобы работать с настройками нужно хорошо различать три разных понятия:
 * 1) Текущее значение настройки: это поле в AR-модели привязанное к текущей настройке при помощи
 *    комбинации полей valutype/valuefield/valueid
 *    (или список моделей, если в настройке допустим множественный выбор)
 *    Как правило текущее значение хранится в моделях класса EasyListItem
 * 2) Значение по умолчанию: это поле в AR-модели привязанное к родительской модели настройки при помощи
 *    комбинации полей valutype/valuefield/valueid
 *    (или список значений по умолчанию, если в настройке допустим множественный выбор)
 *    Таким образом у корневых настроек значений по умолчанию быть не может:
 *    они задаются в коде руками и попадают в базу через миграцию в момент добавления новой настройки
 * 3) Список стандартных значений: это список (EasyList) хранящий элементы (EasyListItem)
 *    названия значения которых используются как предлагаемые варианты выбора при указании
 *    значения настройки
 *    Называется "списком возможных значений" если в настройке разрешено указание своих вариантов
 *    помимо стандартных и "списком допустимых значений" если в значении настройки свои варианты
 *    указывать запрещено :)
 * 4) Список нестандартных (пользовательских) значений: это список всех значений для настройки
 *    которые когда-либо были введены пользователем для этой настройки и не содержатся
 *    в списке стандартных. Любое однажды введенное пользователем значение сохраняется здесь, даже если
 *    оно не выбрано в настройке в данный момент (в этом его отличие от списка выбранных значений)
 *    (только для настроек, в которых разрешен ввод собственных значенй)
 * 5) Список выбранных значений (только для настроек с множественным выбором):
 * 
 * Обратите внимание: список стандартных значений настройки и список значений по умолчанию это разные вещи
 * 
 * Настройка может быть привязана к модели, поэтому важно понимать чем отличается запрос 
 * "список настроек со указанным значением" от запроса
 * "список моделей имеющих настройку с указанным значением"
 * 
 * 
 * Таблица '{{config}}':
 * @property int    $id
 * @property int    $easylistid   - id списка со стандартными значениями этой настройки 
 *                                  (для select-списков)
 *                                  Содержит 0 если стандартных значений не предусмотрено
 * @property int    $userlistid   - id списка со дополнительными значениями для этой настройки
 *                                  Содержит 0 если ввод новых значений пользователем не предусмотрен
 * @property int    $parentid     - id базовой настройки. Базовой считается настройка, которая
 *                                  была использована как шаблон чтобы создать эту модель.
 *                                  Значения, заданные в родительской настройке считаются стандартными
 *                                  значениями для дочерних настроек
 *                                  Самым верхнем уровнем являются системные настройки: 
 *                                  они не привязываются к какому-либо объекту в системе, а служат
 *                                  образцом для создания настроек такого же типа.
 * @property string $name         - служебное имя настройки, маленькие латинские буквы и точки
 *                                  две системные настройки не могут иметь одинаковых названий
 *                                  две корневые настройки одной модели не могут иметь одинаковых названий
 * @property string $title        - название настройки для отображения
 * @property string $description  - пояснение для настройки
 * @property string $type         - тип настройки (чаще всего совпадает с названием input-типа для
 *                                  элемента формы или названием класса виджета, который будет использован
 *                                  для вывода этой настройки)
 * @property int    $minvalues    - минимальное количество значений которые нужно выбрать в этой настройке
 *                                  0   : заполнение необязательно
 *                                  1   : заполнение обязательно
 *                                  n>1 : все что больше единицы: требуется выбрать как минимум n
 *                                        значений, иначе настройка не будет считаться заполненой.
 *                                        Заполнение такой настройки может быть обязательно,
 *                                        но если она не заполнена - мы подставляем значения по 
 *                                        умолчанию из родительской настройки
 * @property int    $maxvalues    - максимальное количество выбранных значений для этой настройки
 *                                  0   : неограничено
 *                                  1   : только одно значение: используется для текстовых строк,  
 *                                        JSON-значений а также для обычных элементов radio или select
 *                                  n>1 : все что больше единицы: ограничение максимального количества
 *                                        одновременно выбранных вариантов
 * @property string $objecttype   - тип объекта к которому привязана настройка: чаще всего здесь
 *                                  указан класс модели к которой привязана эта настройка
 *                                  Для системных настроек это поле всегда содержит значение 'system'
 * @property int    $objectid     - id объекта к которому привязана настройка
 *                                  Содержит 0 если настройка системная или корневая
 * @property int    $timecreated  - дата создания объекта настройки
 *                                  (для настроек, привязанных к моделям как правило совпадает с
 *                                  временем создания модели)
 * @property int    $timemodified - последнее изменение настройки: изменением настройки
 *                                  считается как редактирование свойств самой модели Config так и
 *                                  редактирование поля значения (valuefield) в связанной модели
 *                                  Для настроек с множественным выбором изменением настройки
 *                                  также считается изменение состава элементов списка значений,
 *                                  изменение самих элементов в списке значений
 *                                  а также изменение порядка элементов в списке значений
 *                                  При обновлении связанной модели это поле не изменяется
 * @property string $valuetype    - класс AR-модели, которая содержит значение настройки
 *                                  Если в настройке допустим множественный выбор, то это поле 
 *                                  обязательно должно иметь значение 'EasyList'
 * @property string $valuefield   - поле AR-модели, которое хранит значение настройки
 * @property int    $valueid      - id AR-модели, которая содержит значение настройки
 * @property string|array $value  - псевдо-поле (геттер) для получения значения настройки
 *                                  Если настройка предусматривает максимум одно значение - то 
 *                                  это поле будет содержать строку или число
 *                                  Если в настройке разрешен множественный выбор - то это поле
 *                                  будет содержать массив выбранных значений
 *                                  Если значение настройки не задано - то в этом поле будет null 
 * 
 * Relations:
 * @property Config          $parentConfig     - родительская настройка, из которой была создана эта
 * @property EasyList        $defaultList      - список, содержащий стандартные значения для этой настройки
 * @property EasyListItem[]  $defaultListItems - 
 * @property EasyList        $userList - список, содержащий введенные пользователем значения для
 *                           этой настройки. Используется если ни один из стандартных вариантов 
 *                           не подошел пользователю и если в настройке разрешено добавление 
 *                           собственных значений
 * @property EasyListItem[]  $userListItems  - 
 * @property CActiveRecord   $selectedValue  - модель, содержащая значение настройки 
 *                           (если настройка предусматривает максимум одно значение)
 *                           Если выбранных значений настройки может быть несколько - то эта связь содержит 
 *                           объект-список (EasyListItem) в котором хранятся все выбранные значения
 * @property CActiveRecord[] $selectedValues - все значения этой настройки: массив моделей которые
 *                           считаются выбранными значениями этой настройки
 *                           (только если в настройке разрешен множественный выбор)
 *                           Как правило это элементы списка (EasyListItem)
 *                           Если настройка предусматривает максимум одно значение - то в этом массиве 
 *                           будет только одна модель
 * @property EasyList        $selectedList - список, содержащий выбранные значения
 * @property EasyListItem[]  $selectedListItems -  элементы списка, выбранные в качестве значений 
 *                           этой настройки (только для настроек с множественным выбором) 
 * 
 * 
 * Методы класса CustomRelationSourceBehavior:
 * @method CActiveRecord forModel(CActiveRecord $model)
 * @method CActiveRecord forAnyObject(string $objectType, array|int $objectId, string $operation='AND')
 * @method CActiveRecord forObject(string $objectType, array|int $objectId, string $operation='AND')
 * @method CActiveRecord forEveryObject(string $objectType, array|int $objectId, string $operation='AND')
 * @method CActiveRecord exceptLinkedWithAny(string $objectType, array|int $objectId, string $operation='AND')
 * @method CActiveRecord exceptLinkedWith(string $objectType, array|int $objectId, string $operation='AND')
 * @method CActiveRecord exceptLinkedWithEvery(array $objects, string $operation='AND')
 * @method CActiveRecord withAnyObjectType(array|string $objectTypes, string $operation='AND')
 * @method CActiveRecord withObjectType(array|string $objectTypes, string $operation='AND')
 * @method CActiveRecord withAnyObjectId(array|int $objectIds, string $operation='AND')
 * @method CActiveRecord withObjectId(array|int $objectIds, string $operation='AND')
 * @method CActiveRecord exceptLinkedWithAnyObjectType(array|string $objectTypes, string $operation='AND')
 * @method CActiveRecord exceptLinkedWithEveryObjectType(array|string $objectTypes, string $operation='AND')
 * @method CActiveRecord exceptLinkedWithAnyObjectId(array|int $objectIds, string $operation='AND')
 * @method CActiveRecord exceptLinkedWithEveryObjectId(array|int $objectIds, string $operation='AND')
 * @method CActiveRecord hasCustomValue(string $field, array|string $values, string $operation='AND')
 * @method CActiveRecord exceptCustomValue(string $field, array|string $values, string $operation='AND')
 * @method bool          isUniqueForObject(string $objectType, int $objectId=0, int $extraKeyId=0)
 * 
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
 * 
 * @todo связь selectedListItems которая всегда содержит только элементы easyListItem
 * @todo проверка для максимального/минимального количества значений
 * @todo проверка правильности указания служебного имени
 * @todo прописать все scope-условия в комментариях как "method" чтобы работал codeAssist
 * @todo проверка: две системные настройки не могут иметь одинаковых названий
 * @todo проверка: две корневые настройки одной модели не могут иметь одинаковых названий
 * @todo настроить кэширование значений настроек: к ним часто обращаются и редко изменяют
 * @todo переименовать поле easylistid в defaultlistid
 * @todo проверка при сохранении модели: если в настройке допустим множественный выбор - то
 *       поле valuetype обязательно должно иметь значение 'EasyList'
 * @todo если целевая модель (из которой берется значение) была удалена, то значение настройки
 *       должно быть сброшено на корневое или родительское. Если же ни того ни другого нет - 
 *       то значение valutype/valuefield/valueid сбрасывается на ''/''/'0'
 * @todo решить, нужно ли добавить отдельное поле (value) для хранения значения настройки.
 *       Это упростит поиск по настройкам с одним значением, но добавит необходимость синхронизации
 *       сохраненного значения при каждом изменении связанной модели
 * @todo разрешить не хранить настройки, во всем совпадающие с корневыми (objectid=0)
 *       при попытке получить настроку искать сначала настройку объекта, а если ее нет
 *       то корневую настройку модели (если при этом возможно сохранить работу всех scopes)
 */
class Config extends CActiveRecord
{
    /**
     * @var string - тип настройки:
     */
    const TYPE_TEXT         = 'text';
    /**
     * @var string - тип настройки:
     */
    const TYPE_TEXTAREA     = 'textarea';
    /**
     * @var string - тип настройки: список
     */
    const TYPE_SELECT       = 'select';
    /**
     * @var unknown
     */
    const TYPE_DATETIME     = 'datetime';
    /**
     * @var unknown
     */
    const TYPE_DATE         = 'date';
    /**
     * @var unknown
     */
    const TYPE_COMBODATE    = 'combodate';
    /**
     * @var string - тип настройки:
     */
    const TYPE_CHECKLIST    = 'checklist';
    /**
     * @var string - тип настройки:
     */
    const TYPE_CHECKBOXLIST = 'checklist';
    /**
     * @var string - тип настройки:
     */
    const TYPE_TOGGLE       = 'toggle';
    /**
     * @var string - тип настройки:
     */
    const TYPE_RADIO        = 'radio';
    /**
     * @var string - тип настройки:
     */
    const TYPE_CHECKBOX     = 'checkbox';
    /**
     * @var string - тип настройки:
     */
    const TYPE_SELECT2      = 'select2';
    /**
     * @var string - тип настройки: список с множественным выбором
     */
    const TYPE_MULTISELECT  = 'multiselect';
    /**
     * @var string - тип настройки: адрес ссылки
     */
    const TYPE_URL          = 'url';
    /**
     * @var string - тип настройки: текстовое поле с виджетом "redactor"
     */
    const TYPE_REDACTOR     = 'redactor';
    /**
     * @var string - тип настройки: текстовое поле с виджетом html5-редактора
     */
    const TYPE_WYSIHTML5    = 'wysihtml5';
    
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
			array('objecttype, valuetype, valuefield', 'length', 'max' => 50),
			array('userlistid, easylistid, parentid, minvalues, maxvalues, objectid, valueid, 
			    timecreated, timemodified', 'length', 'max' => 11,
            ),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 * @todo проверять существование привязанного объекта значения если тип значения это клас модели
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// создание новой настройки
	        $listTypes = array(self::TYPE_CHECKBOXLIST, self::TYPE_SELECT, self::TYPE_RADIO);
	        if ( in_array($this->type, $listTypes) )
	        {// для новой настройки создаем список стандартных значений (если требуется)
                $easyList       = new EasyList();
                $easyList->name = 'Список значений для настройки "'.$this->title.'"';
                $easyList->save();
                // привязываем созданный пустой список к этой настройке
                $this->easylistid = $easyList->id;
	        }
	        if ( ! $this->parentid )
	        {// создается корневая настройка
        	    if ( $this->objectid != 0 )
        	    {// наждая настройка должна от чего-то наследоваться
        	        throw new CException('Новые корневые настройки могут быть созданы только для 
        	            моделей или системных настроек с objectid=0');
        	    }
    	    }
    	    if ( $this->forObject($this->objecttype, $this->objectid)->withName($this->name)->exists() )
    	    {// имя настройки должно быть уникальным
    	        if ( $this->objectid == 0 AND $this->objecttype != 'system' )
    	        {
    	            throw new CException('Невозможно создать настройку: 
    	                сочетание objecttype + objectid + name должно быть уникальным для каждой записи.
    	                Только настройка уровня system может иметь более одного objectid=0');
    	        }
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
	    if ( $this->objecttype === 'system' AND $this->objectid != 0 )
	    {// все системные настройки должны придерживаться определенного формата
	        throw new CException('Невозможно сохранить настройку: настройка с типом system
	            может иметь только objectid=0');
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	public function beforeDelete()
	{
	    if ( $this->objecttype == 'system' OR ! $this->parentid OR ! $this->objectid )
	    {// Корневые или системные настройки удаляются только миграцией
	        throw new CException('Корневые или системные настройки удаляются только миграцией');
	    }
	    
	    foreach ( $this->withParentId($this->id)->findAll() as $config )
	    {// обновляем все настройки, наследуемые от этой: убираем ссылку на удаляемую запись
	        // и заменяем ее ссылкой на элемент уровнем выше
	        $config->parentid = $this->parentid;
	        $config->save();
	    }
	    
	    if ( $this->forUserList($this->userList) )
	    {// удаляем введенные пользовательские варианты для настройки при удалении настройки:
	        // список введенных пользователем дополнительных значений
	        // с гораздо меньшей вероятностью где-то используется, поэтому будем искать
	        // ссылки на него только в настройках
	        // @todo каждый раз при привязке списка создавать EasyListInstance чтобы 
	        //       запоминать какие модели нужно проверить перед удалением списка
	        $this->userList->delete();
	    }
	    
	    // @todo удаляем привязаный список стандартных значений (если он больше нигде не используется)
	    return parent::beforeDelete();
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
		    // родительская настройка (из которой создана эта)
		    'parentConfig'      => array(self::BELONGS_TO, 'Config', 'parentid'),
		    // Список (EasyList) содержащий стандартные значения этой настройки
		    'defaultList'       => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
		    // Все стандартные значения этой настройки (EasyListItem) из списка "defaultList"
		    // Эта связь всегда содержит только объекты класса EasyListItem либо пустой массив
		    'defaultListItems'  => array(self::HAS_MANY, 'EasyListItem', array('easylistid' => 'easylistid')),
		    // список нестандартных, введенных пользователем значений (если список разрешено дополнять)
		    'userList'          => array(self::BELONGS_TO, 'EasyList', 'userlistid'),
		    // Все введенные пользователем нестандартные значения настройки
		    // Эта связь всегда содержит только объекты класса EasyListItem либо пустой массив
		    'userListItems'     => array(self::HAS_MANY, 'EasyListItem', array('easylistid' => 'userlistid')),
		    // объект-список (EasyList) содержащий значения для выбранной настройки 
		    // (частный случай связи selectedValue, только для настроек с множественным выбором)
		    'selectedList'      => array(self::BELONGS_TO, 'EasyList', 'valueid'),
		    // список всех выбранных вариантов значений этой настройки 
		    // (как стандартных так пользовательских) 
		    // Используется только для настроек с множественным выбором
		    // Эта связь всегда содержит только объекты класса EasyListItem либо пустой массив
		    'selectedListItems' => array(self::HAS_MANY, 'EasyListItem', array('easylistid' => 'valueid')),
		    //'selectedListItems' => array(self::HAS_MANY, 'EasyListItem', array('id' => 'easylistid'), 'through' => 'selectedList'),
		    // Текущее выбранное значение настройки (для настроек содержащих только одно значение)
		    'selectedListItem'  => array(self::BELONGS_TO, 'EasyListItem', 'valueid'),
		);
		/*if ( $this->getAttribute('valuetype') )
		{
		    // модель в которой хранится значение настройки
		    // (если значение настройки задано как ссылка на поле в другой модели)
		    // @todo не добавлять эту связь если тип значения (valuetype) не является классом модели
		    $relations['selectedValue'] = array(self::BELONGS_TO, $this->getAttribute('valuetype'), 'valueid');
		}*/
		/*if ( $this->getAttribute('valuefield') )
		{
		    // список текущих выбранных значений этой настройки (только для настроек с множественным выбором)
		    // @todo создать связь с использованием through
		    // @todo сразу возвращать готовый список моделей нужных классов если элементы списка
		    //       ссылаются на другие модели в качестве значений
		    $relations['selectedValues'] = array(self::HAS_MANY, 'EasyListItem', 'valueid');
		}*/
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
	            'class'           => 'application.behaviors.EcTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            // изменением настройки считается не только изменение значения модели (Config): 
	            // каждое удаление, изменение или добавление моделей-значений на которые ссылается эта 
	            // настройка должно также обновлять ее timemodified
	            // Этим занимается контроллер виджета редактирования настроек, так как
	            // отслеживать редактирование элементов связанного списка (EasyList)
	            // должен или сам список или связанный с ним контроллер
	            // Отслеживание событий списков из модели настройки - дорогая операция 
	            // да и сама идея не очень вдохновляет
	            // Отслеживание связанных значений в других моделях производится или самими моделями 
	            // (если к ним прикреплено поведение настроек) либо через cron
	            'updateAttribute' => 'timemodified',
	        ),
	        // это поведение позволяет изменять набор связей модели в процессе выборки
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // это поведение добавляет группы условий поиска (scopes)
	        'CustomRelationSourceBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationSourceBehavior',
	            'targetRelationName'  => 'configTarget',
	            'objectTypeField'     => 'objecttype',
	            'objectIdField'       => 'objectid',
	            'customObjectTypes'   => array('system'),
	            'enableEmptyObjectId' => true,
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
			'easylistid' => 'Список стандартных вариантов значений для этой настройки',
		    'userlistid' => 'Список дополнительных вариантов значений для этой настройки (если разрешено добавление своих вариантов помимо стандартных)',
			'parentid' => 'Шаблон для этой настройки',
			'name' => Yii::t('coreMessages', 'name'),
			'title' => Yii::t('coreMessages', 'title'),
			'description' => Yii::t('coreMessages', 'description'),
			'type' => 'Тип настройки',
			'minvalues' => 'Минимальное количество выбранных значений',
			'maxvalues' => 'Максимальное количество выбранных значений (включительно)',
			'valuetype' => 'Тип значения или название класса модели в котором оно хранится',
			'valuefield' => 'Поле, в котором хранится значение',
			'valueid' => 'id модели в которой хранится значение настройки',
		    'timecreated'  => Yii::t('coreMessages', 'timecreated'),
		    'timemodified' => Yii::t('coreMessages', 'timemodified'),
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
	        // все настройки, содержащие несколько значений (множественный выбор)
	        'multipleOnly' => array(
	            'condition' => $this->getTableAlias(true).".`maxvalues` <> 1",
	        ),
	        // все настройки, содержащие только одно значение (текст, число или select с одним варианом)
	        'singleOnly' => array(
	            'condition' => $this->getTableAlias(true).".`maxvalues` = 1",
	        ),
	        // обязательные настройки (должны быть заполнены)
	        'requiredOnly' => array(
	            'condition' => $this->getTableAlias(true).".`minvalues` > 0",
	        ),
	        // необязательные настройки
	        'optionalOnly' => array(
	            'condition' => $this->getTableAlias(true).".`minvalues` = 0",
	        ),
	        // системные настройки (не привязаны к моделям, управляют поведением сайта в целом)
	        'systemOnly' => array(
	            'scopes' => array(
	                'forObject' => array('system', 0),
	            ),
	        ),
	        // только корневые настройки: они действуют для всех моделей класса 
	        // как настройки по умолчанию, их значения используются если модель 
	        // не имеет собственного значения для настройки с указанным именем
	        'rootOnly' => array(
	            'condition' => $this->getTableAlias(true).".`valuetype` IS NOT NULL",
	            'scopes' => array(
	                'withValueType' => array('<>system'),
	                'withValueId'   => array('0'),
	            ),
	        ),
	        // настройки не накследуемые от родительских
	        'withoutParent' => array(
	            'condition' => $this->getTableAlias(true).".`parentid` = 0",
	        ),
	        // настройки у которых предусмотрен набор вариантов для выбора значения
	        // (при этом не важно, пуст этот список или нет)
	        'hasDefaultList' => array(
	            'condition' => $this->getTableAlias(true).".`easylistid` <> 0",
	        ),
	        // настройки у которых предусмотрен ввод дополнительных значений настроек
	        // пользователем (помимо списка стандартных)
	        'hasUserList' => array(
	            'condition' => $this->getTableAlias(true).".`userlistid` <> 0",
	        ),
	        // @todo настройки у которых есть значения по умолчанию
	        /*'hasDefaultValues' => array(
	            'scopes' => array(
	                '' => array('', 0),
	            ),
	        ),*/
	        // @todo hasUserOptions
	    );
	    return CMap::mergeArray($timestampScopes, $modelScopes);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @param  string $className active record class name.
	 * @return Config the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Определить, является ли эта настройка системной (не относящейся ни к одной модели)
	 * 
	 * @return bool
	 */
	public function isSystem()
	{
	    if ( $this->objecttype === 'system' )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, является ли эта настройка корневой (относящейся ко всем моделям одновременно)
	 * 
	 * @return bool
	 */
	public function isRoot()
	{
	    return ! (bool)$this->objectid;
	}
	
	/**
	 * Определить, может ли настройка содержать несколько выбранных значений
	 * 
	 * @return bool
	 */
	public function isMultiple()
	{
	    return $this->withId($this->id)->multipleOnly()->exists();
	}
	
	/**
	 * Определить, должна ли настройка содержать только одно выбранное значение
	 * 
	 * @return bool
	 */
	public function isSingle()
	{
	    return ! $this->isMultiple();
	}
	
	/**
	 * Определить, отличается ли значение настройки от значения по умолчанию
	 * 
	 * @return bool
	 */
	public function isDefaultCopy()
	{
	    if ( $this->isParentCopy() OR $this->isRootCopy() )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, отличается ли значение настройки от родительского
	 * 
	 * @return bool
	 */
	public function isParentCopy()
	{
	    if ( ! $this->parentConfig )
	    {// родительской настройки нет - сравнивать не с чем
	        return false;
	    }
	    if ( $this->valuefield == $this->parentConfig->valuefield AND
    	     $this->valueid    == $this->parentConfig->valueid AND
    	     $this->valuetype  == $this->parentConfig->valuetype )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, отличается ли значение настройки этой модели от общей настройки для всех моделей
	 * 
	 * @return bool
	 */
	public function isRootCopy()
	{
	    if ( $this->isRoot() )
	    {// настройка сама является корневой
	        return false;
	    }
	    if ( ! $rootConfig = $this->getRootConfig() )
	    {// не существует корневой настройки такого типа
	       return false;
	    }
	    if ( $this->valuefield == $rootConfig->valuefield AND 
	         $this->valueid    == $rootConfig->valueid AND
	         $this->valuetype  == $rootConfig->valuetype )
	    {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Определить, заполнена ли эта настройка
	 * (есть ли у нее связанный объект значения)
	 *
	 * @return bool
	 */
	public function isFilled()
	{
	    return (bool)$this->getValueObject();
	}
	
	/**
	 * Подготовить настройку к добавлению нового значения: эта функция вызывается после всех
	 * проверок: она проверяет текущее значение настройки и следит за тем чтобы при правке
	 * обычных настроек не были изменены стандартные или системные 
	 * 
	 * @param unknown $listActions
	 * @param unknown $valueAction
	 * @param unknown $newData
	 * @return bool
	 * 
	 * @todo решить как поступать в ситуации когда настройка содержит списки но не содержит значений
	 */
	public function prepareCreateValue($valueAction='clear', $listActions=array(), $newData=array())
	{
	    if ( ! $this->getValueObject() )
	    {// настройка не содержит значений - подготовка не требуется
	        return true;
	    }
	    if ( $this->getValueObject() AND $this->isDefaultCopy() )
	    {// настройка ссылается на родительское/корневое значение: его нельзя править
	        // при редактировании обычной настройки: создадим копию для изменения
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    return true;
	}
	
	/**
	 * Подготовить настройку к редактированию значения
	 * 
	 * @param unknown $listActions
	 * @param unknown $valueAction
	 * @param unknown $newData
	 * @return bool
	 */
	public function prepareUpdateValue($valueAction='copy', $listActions=array(), $newData=array())
	{
	    if ( ! $this->getValueObject() )
	    {// настройка не содержит значений - нужно создать хотя бы одно
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    if ( $this->isDefaultCopy() )
	    {
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    return true;
	}
	
	/**
	 * Подготовить настройку к редактированию значения
	 * 
	 * @param unknown $listActions
	 * @param unknown $valueAction
	 * @param unknown $newData
	 * @return bool
	 */
	public function prepareDeleteValue($valueAction='copy', $listActions=array(), $newData=array())
	{
	    if ( ! $this->getValueObject() )
	    {// настройка не содержит значений - нужно создать хотя бы одно
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    if ( $this->isDefaultCopy() )
	    {
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    return true;
	}
	
	/**
	 * Получить готовое значение текущей модели настройки ($this)
	 * Должно применяться только для уже созданных записей (isNewRecord=false)
	 * 
	 * @param bool $defaultOnEmpty - подставить значение по умолчанию если сама настройка
	 *                               значения не содержит
	 * @return string|array - текущее значение настройки
	 *                        * (string|int) строка или число 
	 *                          (для настроек содержащих максимум одно выбранное значение)
	 *                        * (array) массив строк
	 *                          (для настроек которые могут содержать несколько выбранных значений)
	 * 
	 * @todo проверка: в objecttype всегда должен быть корректный класс модели или пустота
	 * @todo решить что возвращать в случае когда значение настройки - объект с неуказанным полем
	 *       (пока что возвращаем весь объект целиком)
	 * @todo кеширование после извлечения
	 */
	public function getValue($defaultOnEmpty=true, $forNewRecord=false)
	{
	    if ( $this->isNewRecord AND ! $forNewRecord )
	    {// эта функция должна работать только с реально существующими моделями
	        throw new CException('Невозможно получить значение из не сохраненной настройки');
	    }
	    $result     = null;
	    $modelClass = $this->valuetype;
	    
	    if ( $this->isSingle() )
	    {// получаем одно значение
	        $field  = $this->valuefield;
	        $id     = $this->valueid;
	        
	        if ( ! $modelClass OR ! $id  )
	        {// значение настройки пусто - пробуем взять стандартное
	            if ( $defaultOnEmpty )
	            {
	                $result = $this->getDefaultValue();
	            }else
	            {
	                return $result;
	            }
	        }
	        if ( $field AND $id )
	        {// указано поле модели: значение из него будет считаться значением настройки
	            if ( $model = $modelClass::model()->findByPk($id) )
	            {// модель-источник значения найдена
	                $result = $model->$field;
	            }else
	            {// модель, из которой нужно взять значение не найдена, хотя настройка
	                // содержала ненулевой id модели значения
	                // @todo записать ошибку в лог, очистить ссылку на удаленную модель: 
	                //       только системные настройки могут иметь нулевой valueid
	            }
	        }else
	        {// поле модели неизвестно: возвращаем весь объект целиком
	            $result = $modelClass::model()->findByPk($id);
	        }
	    }else
	    {// получаем список выбранных вариантов (это всегда EasyListItem) и извлекаем данные из каждого
	        $result = $this->getSelectedOptions();
	        if ( ! $result AND $defaultOnEmpty )
	        {// если выбраных значений не нашлось - то берем значения по умолчанию
	            $result = $this->getDefaultValue();
	        }
	    }
	    return $result;
	}
	
	/**
	 * 
	 * @return CActiveRecord
	 */
	public function getValueObject()
	{
	    if ( ! is_subclass_of($this->valuetype, 'CActiveRecord') OR ! $this->valueid )
	    {// значение настройки не связано с каким-либо объектом
	        return null;
	    } 
	    return CActiveRecord::model($this->valuetype)->findByPk($this->valueid);
	}
	
	/**
	 * Получить корневую настройку по умолчанию (берется или из родительской или из корневой)
	 * 
	 * @return Config
	 */
	public function getDefaultConfig()
	{
	    if ( $this->isSystem() OR $this->isRoot() AND ! $this->parentConfig )
	    {// у не наследуемых системных настроек не может быть значений по умолчанию 
	        return $this;
	    }
	    if ( $this->parentConfig )
	    {// родительская настройка имеет больший приоритет при обращении для того чтобы
	        // можно было задавать свою иерархию наследования
	        return $this->parentConfig;
	    }
	    if ( $rootConfig = $this->getRootConfig() )
	    {// корневая настройка - это настройка с objecttype='класс модели' и objectid
	        return $rootConfig;
	    }
	    return null;
	}
	
	/**
	 * Получить корневую настройку модели (настройку по умолчанию из которой 
	 * создаются все новые объекты настроек)
	 * 
	 * @return Config
	 */
	public function getRootConfig()
	{
	    return $this->forObject($this->objecttype, 0)->withName($this->name)->find();
	}
	
	/**
	 * Получить готовое значение текущей модели настройки ($this)
	 * 
	 * @return string|array - значение по умолчанию для этой настройки
	 *                        * (string|int) строка или число 
	 *                          (для настроек содержащих максимум одно выбранное значение)
	 *                        * (array) массив строк
	 *                          (для настроек которые могут содержать несколько выбранных значений)
	 * 
	 * @todo решить нужно ли искать корневую настройку модели если в родительской нет значения
	 */
	public function getDefaultValue()
	{
	    if ( $this->isSystem() OR $this->isRoot() )
	    {// у не наследуемых системных настроек не может быть значений по умолчанию 
	        // @todo у них такими значениями всегда считаются их собственные
	        return $this->getValue(false);
	    }
	    if ( $defaultConfig = $this->getDefaultConfig() )
	    {
	        return $defaultConfig->value;
	    }
	    if ( $this->isMultiple() )
	    {
	        return array();
	    }
	    return null;
	}
	
	/**
	 * Получить корневое значение для этой настройки
	 * 
	 * @return string|array - корневое значение для этой настройки
	 *                        * (string|int) строка или число 
	 *                          (для настроек содержащих максимум одно выбранное значение)
	 *                        * (array) массив строк
	 *                          (для настроек которые могут содержать несколько выбранных значений)
	 */
	public function getRootValue()
	{
	    if ( ! $rootConfig = $this->getRootConfig() )
	    {// не существует корневой настройки такого типа
	        return null;
	    }
	    return $rootConfig->value;
	}
	
	/**
	 * Получить значения из элементов списка стандартных значений (EasyListItem)
	 * допустимых в этой настройке
	 * Эта функция получает только значения
	 * Если вам нужны модели целиком используйте $this->defaultListItems
	 * 
	 * @param bool $removeSelected - что делать с теми элементами, которые уже выбраны? 
	 *                               * true   - ничего не делать
	 *                               * false  - убрать из списка
	 * @return array
	 */
	public function getDefaultOptions($removeSelected=true)
	{
	    $result = array();
	    foreach ( $this->defaultListItems as $item )
	    {
	        $result[$item->id] = $item->data;
	    }
	    return $result;
	}
	
	/**
	 * Получить значения из элементов списка пользовательских значений (EasyListItem)
	 * добавленых для этой настройки
	 * Эта функция получает только значения
	 * Если вам нужны модели целиком используйте $this->userListItems
	 *
	 * @param bool $removeSelected - что делать с теми элементами, которые уже выбраны?
	 *                               * true   - ничего не делать
	 *                               * false  - убрать из списка
	 * @return array
	 */
	public function getUserOptions($includeSelected=true)
	{
	    $result = array();
	    foreach ( $this->userListItems as $item )
	    {
	        $result[$item->id] = $item->data;
	    }
	    return $result;
	}
	
	/**
	 * Получить выбранные значения настройки из элементов списка (EasyListItem)
	 * Эта функция получает только значения
	 * Если вам нужны модели целиком используйте $this->selectedListItems
	 *
	 * @return array
	 */
	public function getSelectedOptions()
	{
	     $result = array();
	     foreach ( $this->selectedListItems as $item )
	     {/* @var $item EasyListItem */
	         $result[$item->id] = $item->data;
	     }
	     return $result;
	}
	
	/**
	 * Именованая группа условий: все настройки, использующие этот список вариантов значений
	 *
	 * @param  int|array|EasyList $defaultList - id списка стандартных значений настройки, сама модель
	 *                                           такого списка (EasyList), или массив id таких списков
	 * @return Config
	 */
	public function forDefaultList($defaultList, $operation='AND')
	{
	    if ( is_object($defaultList) )
	    {// передан объект целиком но нам нужен из него только id
	        $id = $defaultList->id;
	    }else
	    {
	        $id = $defaultList;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`easylistid`', $id);
	
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки, использующие список дополнительных значений настройки
	 *
	 * @param  int|array|EasyList $userList - id списка пользовательских значений настройки, сама модель
	 *                                        такого списка (EasyList), или массив id таких списков
	 * @return Config
	 */
	public function forUserList($userList, $operation='AND')
	{
	    if ( is_object($userList) )
	    {// передан объект целиком но нам нужен из него только id
	        $id = $userList->id;
	    }else
	    {
	        $id = $userList;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`userlistid`', $id);
	
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки c указанным служебным названием или все настройки
	 * с указанными названиями, если $name передан как массив
	 * 
	 * @param  string|array $name - служебное название настройки (или список названий)
	 * @return Config
	 */
	public function withName($name, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`name`', $name);
	
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все дочерние настройки относящиеся к указанной родительской или
	 * к любой из указанных родительских настроек если передан массив
	 * 
	 * @param  int|array $parentId - id родительской настройки или массив таких id
	 * @return Config
	 */
	public function withParentId($parentId, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`parentid`', $parentId);
	
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки c указанным значением valuetype
	 * 
	 * @param  int|array $valueId - значение в поле valuetype или массив таких значений
	 * @return Config
	 */
	public function withValueType($valueType, $operation='AND')
	{
	    if ( ! $valueType )
	    {// условие не используется
	       return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`valuetype`', $valueType);
	
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки c указанным значением valueid
	 * 
	 * @param  int|array $valueId - id значения в поле valueid или массив таких id
	 * @return Config
	 */
	public function withValueId($valueId, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`valueid`', $valueId);
	
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: все настройки с выбранным значением
	 * 
	 * @param  array|string $value - требуемое значение в поле value для связанной модели
	 * @return Config
	 * 
	 * @todo функция withOptionId все multiple-настройки с указанным EasyListItem.id 
	 *       в привязаном объекте значения (получаем одно или несколько EasyListItem.id 
	 *       из стандартного (или пользовательского) списка занчений и ищем в своем 
	 *       списке ссылки на них)
	 */
	public function withSelectedValue($value, $operation='AND')
	{
	    if ( ! $value )
	    {// условие не используется
	        return $this;
	    }
	    if ( $this->isMultiple() )
	    {// настройка с множественным выбором всегда ссылается на список значений (EasyList)
	        $with = array(
	            'selectedListItems' => array(
	                'select'   => false,
	                'joinType' => 'INNER JOIN',
    	            'scopes' => array(
        	            'withValue' => array($value),
                    ),
                ),
	        );
	    }else
	    {// настройка с одиночным выбором всегда ссылается на значение в поле другой модели
	        /* @var $model CActiveRecord */
	        $model = $this->valuetype;
	        $alias = $model::model()->getTableAlias(true);
	        // используем методы CDbCriteria для обработки случая с несколькими $value
	        $condition = new CDbCriteria();
            $condition->compare("{$alias}.`{$this->valuefield}`", $value);
	        // создаем условие поиска по значению связанного поля
	        $with = array(
	            'selectedListItem' => array(
	                'select'    => false,
	                'joinType'  => 'INNER JOIN',
                    'condition' => $condition,
    	        ),
    	    );
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with     = $with;
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * (alias) Получить все настройки в которых используется (выбран) переданый вариант стандартного 
	 * значения настройки (как из стандартного списка вариантов так и из пользовательского)
	 * 
	 * @param  int|array|EasyListItem $option
	 * @return Config
	 */
	public function withSelectedOption($option, $operation='AND')
	{
	    return $this->withAnySelectedOption($option, $operation);
	}
	
	/**
	 * Получить все настройки в которых используется (выбран) переданый вариант стандартного
	 * значения настройки (как из стандартного списка вариантов так и из пользовательского)
	 *
	 * @param  int|array|EasyListItem $option
	 * @return Config
	 */
	public function withAnySelectedOption($options, $operation='AND')
	{
	    if ( ! $option )
	    {// условие не используется
	       return $this;
	    }
	    if ( is_object($option) )
	    {// передана одна модель с вариантом значения
	       $optionId = $option->id;
	    }elseif ( is_array($option) )
	    {// передано несколько вариантов, будем искать любое совпедение
    	    $optionList = $option;
    	    $optionId   = array();
    	    foreach ( $optionList as $optionItem )
    	    {// проверим что за массив нам передали: масив id или массив элементов списка (EasyListItem)
        	    if ( is_object($optionItem) )
        	    {
        	        $optionId[] = $optionItem->id;
        	    }elseif ( is_numeric($option) )
        	    {
        	        $optionId[] = $optionItem;
        	    }else
        	    {
        	        throw new CException('Неправильно указан параметр $option (поиск по элементу списка)');
        	    }
    	    }
	    }elseif ( is_numeric($option) )
	    {// передан id модели
            $optionId = $option;
	    }else
	    {
	        throw new CException('Неправильно указан параметр $option (поиск по элементу списка)');
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'selectedListItems' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes' => array(
	                'withId' => array($optionId),
	            ),
	        ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * Получить все настройки в которых используется (выбран) переданый вариант стандартного
	 * значения настройки (как из стандартного списка вариантов так и из пользовательского)
	 *
	 * @param  int|array|EasyListItem $option
	 * @return Config
	 */
	public function withEverySelectedOption($options, $operation='AND')
	{
        
	}
	
	/**
	 * Определить является ли переданный вариант значения настройки стандартным значением
	 * или ссылкой на такое значение
	 *
	 * @param  EasyListItem $option
	 * @return bool
	 */
	public function isDefaultOption($option)
	{
        if ( $option->easylistid == $this->easylistid  )
        {// значение содержится в списке стандартных
            return true;
        }
        $defaults = $this->getDefaultOptions();
        if ( isset($defaults[$option->id]) OR 
           ( isset($defaults[$option->objectid]) AND $option->objecttype == $this->defaultList->itemtype ) )
        {// значение является ссылкой на стандартное
            return true;
        }
        return false;
	}
	
	/**
	 * Определить является ли переданный вариант значения настройки пользовательским
	 *
	 * @param  EasyListItem $option
	 * @return bool
	 */
	public function isUserOption($option)
	{
	    return ! $this->isDefaultOption($option);
	}
	
	/**
	 * Восстановить изначальное значение настройки
	 * 
	 * @return bool
	 * 
	 * @todo доработать с учетом одинарных/множественных настроек
	 */
	public function restoreDefault()
	{
	    if ( $this->objecttype == 'EasyList' AND $this->valueid == $this->easylistid )
	    {// список настроек совпадает со стандартным
	        return true;
	    }
	    foreach ( $this->selectedListItems as $item )
	    {// удаляем выбранные значения
	        $item->delete();
	    }
	    foreach ( $this->defaultListItems as $defaultItem )
	    {// копируем список значений из настройки с данными по умолчанию
	        $attributes = $defaultItem->attributes;
	        // устанавливаем в новых моделях id списка с выбранными элементами
	        $attributes['easylistid'] = $this->valueid;
	        // удаляем лишние данные из модели перед копированием
	        unset($attributes['id']);
	        unset($attributes['timecreated']);
	        unset($attributes['timemodified']);
	        unset($attributes['sortorder']);
	        unset($attributes['status']);
	        
	        $itemCopy = new EasyListItem;
	        $itemCopy->setAttributes($attributes);
	        $itemCopy->save();
	    }
	    return true;
	}
	
	/**
	 * Скопировать значения настройки из стандартного объекта в текущий перед изменением
	 * чтобы не задеть настройки по умолчанию 
	 * 
	 * @return void
	 */
	protected function createDataFromDefault($valueAction='clear', $listActions=array(), $newData=array())
	{
	    if ( empty($listActions) )
	    {
	        $listActions = array(
	            'userlistid' => 'new',
	        );
	    }
	    $default = $this->getDefaultConfig();
	    $this->attributes = $newData;
	    
	    foreach ( $listActions as $idField => $action )
	    {
	        $this->$idField = $this->createListFromDefault($default, $idField, $action);
	    }
	    if ( $valueAction === 'clear' )
	    {// очистить значение
	        $this->valuetype  = null;
	        $this->valuefield = null;
	        $this->valueid    = null;
	    }
	    if ( $valueAction === 'copy' )
	    {
	        if ( $this->isMultiple() )
	        {
	            $this->valueid = $this->createListFromDefault($default, 'valueid', 'copy');
	        }else
	        {
	            $item = new EasyListItem();
	            $item->easylistid  = $this->userlistid;
	            $item->objecttype  = 'EasyListItem';
	            $item->objectfield = 'value';
	            $item->objectid    = 0;
	            $item->save();
	            $item->objectid = $item->id;
	            $item->save();
	            
	            $this->objecttype = 'EasyListItem';
	            $this->objectid   = $item->id;
	            $this->valuefield = 'value';
	        }
	    }
	    return $this->save();
	}
	
	/**
	 * Создать новый список из настройки
	 * 
	 * @param  Config $config
	 * @return void
	 */
	protected function createListFromDefault($config, $type='easylistid', $action='copy')
	{
	    if ( $action = 'clear' OR ! $config->$type )
	    {
	        return 0;
	    }
	    switch ( $type )
	    {
	        case 'easylistid': $relation = 'defaultList'; break;
	        case 'userlistid': $relation = 'userList'; break;
	        case 'valueid':    $relation = 'selectedList'; break;
	    }
	    $defaultList = $default->$relation;
	    $attributes  = $default->$relation->attributes;
	    
        unset($attributes['id']);
        unset($attributes['timecreated']);
        unset($attributes['timemodified']);
        unset($attributes['lastupdate']);
        unset($attributes['lastcleanup']);
        
        $newList = new EasyList();
        $newList->attributes = $attributes;
        $newList->save();
        if ( $action === 'copy' )
        {
            foreach ( $defaultList->listItems as $item )
            {
                $itemAttributes = $item->attributes;
                unset($itemAttributes['id']);
                unset($itemAttributes['timecreated']);
                unset($itemAttributes['timemodified']);
                unset($itemAttributes['easylistid']);
                unset($itemAttributes['sortorder']);
                
                $itemCopy = new EasyListItem();
                $itemCopy->attributes = $itemAttributes;
                $itemCopy->easylistid = $newList->id;
                $itemCopy->save();
            }
        }
        return $newList->id;
	}
	
	/**
	 * Функция подготовки первого изменения значения или списка значений
	 * для этой настройки (ConfigValue): мы храним только те значения настроек которые
	 * отличаются от значений по умолчанию, поэтому при первом изменении значения любой настройки
	 * мы должны ее инициализировать
	 * Эта функция вызывается из модели ConfigValue перед сохранением редактированием или обновлением
	 *
	 * @param  string      $operation - действие производимое с настройкой (ConfigValue):
	 *                                  insert/update/delete
	 * @param  ConfigValue $configValue - создаваемое, обновляемое или удаляемое значение настройки
	 * @return bool
	 *
	 * @deprecated переместить в контроллер для настроек
	 */
	/*public function beforeFirstEdit($operation, $configValue)
	{
	    if ( $this->timemodified OR ! $this->parentid OR $this->isSystem() )
	    {// это не первое редактирование настройки или настройка корневая/системная:
	        // не требуется никаких дополнительных операций подготовки при изменении ее записей
	        return true;
	    }
	    if ( ! $this->parentConfig )
	    {// проверяем целостность иерархии настроек
	        throw new CException('Невозможно изменить настройку: она ссылается на несуществующую
	            родительскую запись');
	    }
	    $this->timemodified = time();
	     
	    // определим какая операция выполняется
	    switch ( $operation )
	    {
	        // добавляется новое значение настройки
	        case 'insert':
	            if ( $this->isMultiple() )
	            {// у настройки может быть несколько значений
	                 
	            }else
	            {// у настройки может быть только одно значение
	                 
	            }
	            if ( ! $this->parentConfig->configValues )
	            {// в родительской настройке нет значений по умолчанию: она или не обязательна
	                // к заполнению или не имеет значений по умолчанию
	                // создаем и сохраняем новую запись из переданных значений
	            }
	            break;
	            // обновляется текущее значение настройки
	        case 'update':
	
	        break;
	        // удаляется одно из добавленых значений настройки
	        case 'delete':
	
	        break;
	        default: throw new CException('Неизвестный тип операции с настройкой:'.$operation);
	    }
	    return $this->save();
    }*/
}