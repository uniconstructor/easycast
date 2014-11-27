<?php

/**
 * Модель для работы с настройками приложения.
 * Настройки могут быть прикреплены к любой модели в системе. 
 * Значения настроек хранятся отдельно, в таблице config_values.
 * 
 * Чтобы работать с настройками нужно различать понятия:
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
 *    Те значения из стандартного или пользовательского списка которые были
 *    выбраны в качестве значения настройки
 * 
 * Обратите внимание: список стандартных значений настройки и список значений 
 * по умолчанию это разные вещи
 * Настройка может быть привязана к модели, поэтому важно понимать чем отличается запрос 
 * "список настроек со указанным значением" от запроса
 * "список моделей имеющих настройку с указанным значением"
 * 
 * Правила создания и удаления настроек:
 * 1) Общий принцип для всех настроек: не хранить то что можно не хранить
 * 2) До тех пор пока настройка не изменена считается что ее значение
 *    совпадает со значением по умолчанию (взятое из корневой настройки)
 * 3) Если дочерняя настройка уже создана - то она удаляется или вместе с моделью или 
 *    при сбросе значения до стандартного
 * 4) Стандартный способ сброса настройки до значения по умолчанию - это удаление настройки (Config), 
 *    поэтому корневые и системные настройки не сбрасываются
 * 5) Список выбранных значений настройки не может совпадать со списком стандартных значений
 *    или со списком пользовательских значений
 * 6) При копировании корневой настройки список стандартных значений всегда сохраняется
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
 * @property string  $valuetype   - класс AR-модели, которая содержит значение настройки
 *                                  Если в настройке допустим множественный выбор, то это поле 
 *                                  обязательно должно иметь значение 'EasyList'
 * @property string  $valuefield  - поле AR-модели, которое хранит значение настройки
 * @property int     $valueid     - id AR-модели, которая содержит значение настройки
 * @property string|array $value  - псевдо-поле (геттер) для получения значения настройки
 *                                  Если настройка предусматривает максимум одно значение - то 
 *                                  это поле будет содержать строку или число
 *                                  Если в настройке разрешен множественный выбор - то это поле
 *                                  будет содержать массив выбранных значений
 *                                  Если значение настройки не задано - то в этом поле будет null 
 * @property int $allowuservalues - разрешить/запретить пользователям вводить собственные значения 
 *                                  настройки помимо стандартных
 * @property int $schemaid        - id схемы документа, которая хранит форму для этой настройки
 * 
 * Relations:
 * @property Config          $parentConfig     - родительская настройка, из которой была создана эта
 * @property EasyList        $defaultList      - список, содержащий стандартные значения для этой настройки
 * @property EasyListItem[]  $defaultListItems - все значения из списка по умолчанию
 * @property EasyList        $userList         - список, содержащий введенные пользователем значения для
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
 * @property EasyList        $selectedList      - список, содержащий выбранные значения
 * @property EasyListItem[]  $selectedListItems - элементы списка, выбранные в качестве значений настройки
 *                                                (только для настроек с множественным выбором)
 * @property DocumentSchema  $formSchema - схема документа, по которой составляется форма редактирования настройки
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
 * @todo использовать схему документа для описания формы редактирования настройки
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
     * @var string - тип настройки:
     */
    const TYPE_DATETIME     = 'datetime';
    /**
     * @var string - тип настройки:
     */
    const TYPE_DATE         = 'date';
    /**
     * @var string - тип настройки:
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
		    array('allowuservalues', 'boolean'),
			array('userlistid, easylistid, parentid, minvalues, maxvalues, objectid, valueid, 
			    timecreated, timemodified, schemaid', 'length', 'max' => 11,
            ),
		);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 * 
	 * @return bool
	 * @throws CException
	 * 
	 * @todo проверять существование привязанного объекта значения если тип значения это класc модели
	 * @todo вынести проверки существования и уникальности в rules()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// создание новой настройки
	        if ( ! $this->parentid )
	        {// создается корневая настройка
        	    if ( $this->objectid != 0 )
        	    {// наждая настройка должна от чего-то наследоваться
        	        throw new CException('Новые корневые настройки могут быть созданы только для 
        	            моделей или системных настроек с objectid=0');
        	    }
    	    }else
    	    {// создается обычная настройка
    	        $this->easylistid = $this->parentConfig->easylistid;
    	        if ( $this->parentConfig->allowuservalues )
    	        {// в настройке разрешено добавление собственных значений помимо стандартных
    	            // создаем специальный список для них
    	            $userList       = new EasyList();
    	            $userList->name = 'Список дополниельных для настройки "'.$this->title.'"';
    	            $userList->save();
    	            // привязываем созданный пустой список к этой настройке
    	            $this->userlistid = $userList->id;
    	        }
    	    }
    	    if ( $this->maxvalues != 1 AND ! $this->easylistid )
    	    {// для новой настройки создаем список стандартных значений 
    	        // (если в родительской настройке такой список не задан
    	        // и если настройка будет содержатьне одно значение -
    	        // то для такой настройки нужно создать новый список)
        	    $easyList       = new EasyList();
        	    $easyList->name = 'Список значений для настройки "'.$this->title.'"';
        	    $easyList->save();
        	    // привязываем созданный пустой список к этой настройке
        	    $this->easylistid = $easyList->id;
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
	 * 
	 * @return bool
	 * @throws CException
	 * 
	 * @todo проверять результат обновления дочерних настроек
	 */
	public function beforeDelete()
	{
	    if ( $this->isSystem() OR $this->isRoot() )
	    {// корневые или системные настройки удаляются только миграцией
	        $msg = 'Корневые или системные настройки удаляются только миграцией (id='.$this->id.')';
	        Yii::log($msg, CLogger::LEVEL_WARNING, 'application.config');
    	    throw new CException($msg);
    	    return false;
	    }
	    // удаление связанных записей происходит только через транзакцию
	    $transaction = $this->getDbConnection()->beginTransaction();
	    foreach ( $this->childrenConfig as $config )
	    {// обновляем все настройки, наследуемые от этой: убираем ссылку на удаляемую запись
	        if ( $this->parentConfig )
	        {// если возможно - заменяем ее ссылкой на элемент уровнем выше
	            $config->parentid = $this->parentid;
	            if ( $config->save() )
	            {// ссылка на удаляемую настройку успешно заменена
	                continue;
	            }
	            $msg = '(Не удалось заменить parentid для дочерней настройки (id='.$config->id.')';
	            Yii::log($msg, CLogger::LEVEL_ERROR, 'application.config');
	        }
	        if ( $rootConfig = $this->getRootConfig() )
	        {// или корневой настройкой
	            $config->parentid = $rootConfig->id;
	            if ( $config->save() )
	            {// настройка успешно обновлена - можно переходить к следующей
	                continue;
	            }
	            $msg = '(Не удалось заменить parentid для дочерней настройки (id='.$config->id.')';
	            Yii::log($msg, CLogger::LEVEL_ERROR, 'application.config');
	        }
	        // если заменить удаляемую настройку нечем - удаляем ссылку на нее, 
	        // но оставляем сами дочерние настройки
	        $config->parentid = 0;
	        if ( ! $config->save() )
	        {// не удалось заменить ссылку на удаляемую настройку
	            $transaction->rollback();
	            // сохраняем ошибку в лог
	            $msg = '(Не удалось заменить parentid для дочерней настройки (id='.$config->id.')';
	            Yii::log($msg, CLogger::LEVEL_ERROR, 'application.config');
	            // и прерываем удаление
	            throw new CException('Невозможно обнулить parentid для дочерней настройки (id='.$config->id.')');
	            return false;
	        }
	    }
	    // завершаем транзакцию
	    $transaction->commit();
	    
	    return parent::beforeDelete();
	}
	
	/**
	 * @see CActiveRecord::afterDelete()
	 */
	public function afterDelete()
	{
	    if ( $this->userList )
	    {// удаляем введенные пользовательские варианты для настройки при удалении настройки
    	    // (список будет удален только если он не используется в системе)
    	    $this->userList->delete();
	    }
	    parent::afterDelete();
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
		    // родительская настройка (из которой создана эта)
		    'parentConfig'      => array(self::BELONGS_TO, 'Config', 'parentid'),
		    // дочерние настройки
		    'childrenConfig'    => array(self::HAS_MANY, 'Config', 'parentid'),
		    // Список (EasyList) содержащий стандартные значения этой настройки
		    'defaultList'       => array(self::BELONGS_TO, 'EasyList', 'easylistid'),
		    // Все стандартные значения этой настройки (EasyListItem) из списка "defaultList"
		    // Эта связь всегда содержит только объекты класса EasyListItem либо пустой массив
		    'defaultListItems'  => array(self::HAS_MANY, 'EasyListItem', array('id' => 'easylistid'),
		        'through' => 'defaultList',
		    ),
		    // список нестандартных, введенных пользователем значений (если список разрешено дополнять)
		    'userList'          => array(self::BELONGS_TO, 'EasyList', 'userlistid'),
		    // Все введенные пользователем нестандартные значения настройки
		    // Эта связь всегда содержит только объекты класса EasyListItem либо пустой массив
		    'userListItems'     => array(self::HAS_MANY, 'EasyListItem', array('id' => 'easylistid'),
		        'through' => 'userList',
		    ),
		    // объект-список (EasyList) содержащий значения для выбранной настройки 
		    // (частный случай связи selectedValue, только для настроек с множественным выбором)
		    'selectedList'      => array(self::BELONGS_TO, 'EasyList', 'valueid'),
		    // список всех выбранных вариантов значений этой настройки (как стандартных так пользовательских) 
		    // Используется только для настроек с множественным выбором
		    // Эта связь всегда содержит только объекты класса EasyListItem либо пустой массив
		    'selectedListItems' => array(self::HAS_MANY, 'EasyListItem', array('id' => 'easylistid'),
		        'through' => 'selectedList',
		    ),
		    // Текущее выбранное значение настройки (для настроек содержащих только одно значение)
		    'selectedListItem'  => array(self::BELONGS_TO, 'EasyListItem', 'valueid'),
		    // схема документа для создания форма редактирования настройки
		    'formSchema'        => array(self::BELONGS_TO, 'DocumentSchema', 'schemaid'),
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
		    'allowuservalues' => 'Разрешить ли пользователям вводить собственные значения помимо стандартных?',
		    'schemaid' => 'Схема формы',
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
	    return ($this->maxvalues != 1);
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
	    // @todo решить нужна ли проверка $this->valuefield ==  $this->parentConfig->valuefield AND ...
	    if ( $this->valueid    == $this->parentConfig->valueid AND
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
	    // @todo решить нужна ли проверка $this->valuefield == $rootConfig->valuefield AND ...
	    if ( $this->valueid   == $rootConfig->valueid AND
	         $this->valuetype == $rootConfig->valuetype )
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
	 * Определить, редактировалась ли эта настройка для указанного объекта хотя бы раз 
	 *
	 * @param  string $objectType
	 * @param  string $objectId
	 * @return bool
	 */
	public function isModifiedFor($objectType, $objectId)
	{
	    if ( Config::model()->withName($this->name)->forObject($objectType, $objectId)->exists() )
	    {// настройка привязана к указанной модели - значит она была скопирована при изменении
	        return true;
	    }
        if ( Config::model()->withName($this->name)->forObject($objectType, 0)->exists() )
        {// используется корневая настройка - значит она ни разу не редактировалась
            return false;
        }
        // такая настройка вообще не предусмотрена для такого типа объектов
        // @todo записать в лог
        return false;
	}
	
	/**
	 * Подготовить настройку к добавлению нового значения: эта функция вызывается после всех
	 * проверок: она проверяет текущее значение настройки и следит за тем чтобы при правке
	 * обычных настроек не были изменены стандартные или системные 
	 * 
	 * @param  string $valueAction
	 * @param  array  $listActions
	 * @param  array  $newData
	 * @return bool
	 * 
	 * @deprecated
	 */
	public function prepareCreateValue($valueAction='clear', $listActions=array(), $newData=array())
	{
	    if ( ! $this->getValueObject() )
	    {// настройка не содержит значений - подготовка не требуется
	        return true;
	    }
	    if ( $this->getValueObject() AND $this->isDefaultCopy() )
	    {// значение настройки отличается от значения по умолчанию, и при этом
	        // настройка ссылается на родительское/корневое значение: его нельзя править
	        // при редактировании обычной настройки: создадим копию для изменения
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    return true;
	}
	
	/**
	 * Подготовить настройку к редактированию значения
	 * 
	 * @param  string $valueAction
	 * @param  array $listActions
	 * @param  array $newData
	 * @return bool
	 * 
	 * @deprecated
	 */
	public function prepareUpdateValue($valueAction='copy', $listActions=array(), $newData=array())
	{
	    if ( ! $this->getValueObject() )
	    {// настройка не содержит значений - нужно создать хотя бы одно
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    if ( $this->isDefaultCopy() )
	    {// значение настройки отличается от значения по умолчанию
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    return true;
	}
	
	/**
	 * Подготовить настройку к редактированию значения
	 * 
	 * @param  string $valueAction
	 * @param  array  $listActions
	 * @param  array  $newData
	 * @return bool
	 * 
	 * @deprecated
	 */
	public function prepareDeleteValue($valueAction='copy', $listActions=array(), $newData=array())
	{
	    if ( ! $this->getValueObject() )
	    {// настройка не содержит значений - нужно создать хотя бы одно
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    if ( $this->isDefaultCopy() )
	    {// значение настройки отличается от значения по умолчанию
	        return $this->createDataFromDefault($listActions, $valueAction, $newData);
	    }
	    return true;
	}
	
	/**
	 * Получить настройку для редактирования пользователем
	 * Любая настройка создается и привязывается к модели только после первого редактирования:
	 * до этого момента вместо нее используется значение из корневой настройки
	 * (как значение по умолчанию)
	 * Этот метод должен быть вызван перед редактированием любой настройки модели
	 * Проверяет привязана ли это настройка к указанной модели, если нет - то создает
	 * копию настройки (которую можно редактировать пользователю в отличие от корневой) 
	 * и привязывает ее к модели
	 * 
	 * @param  string $objectType - тип модели для которой редактируется настройка
	 * @param  string $objectId   - id модели для которой редактируется настройка
	 * @param  string $newValue   - новое значение настройки, передается при попытке редактирования
	 *                              значения настройки, возможно несколько вариантов: 
	 *                              1) строка или число, если для настройки не предусмотрен
	 *                                 список стандартных значений и она не содержит список
	 *                              2) id модели указанной в valuetype если это одиночная
	 *                                 настройка и она содержит список стандартных значений
	 *                              3) массив моделей из списка если настройка имеет список
	 *                                 стандартных значений и позволяет множественый выбор
	 *                              4) массив id моделей если настройка имеет список
	 *                                 стандартных значений и позволяет множественый выбор
	 *                              5) пустой массив если из изначального списка значений
	 *                                 настройки нужно удалить все элементы
	 *                              6) пустая строка или 0 для настроек с одним значением
	 *                                 если нужно очистить значение настройки
	 * @return Config
	 * 
	 * @todo дописать работу с параметром $newValue
	 */
	public function getEditableConfig($objectType, $objectId, $newValue=null)
	{
	    if ( $this->isModifiedFor($objectType, $objectId) )
	    {// эта настройка для этого объекта уже как минимум раз редактировалась,
	        // а значит уже привязана к этой модели
	        return $this;
	    }
	    // это корневая настройка, подставленная по умолчанию вместо настройки модели
	    // нужно создать ее копию и привязать ее к указанной модели:
	    // после этого настройку можно будет редактировать
	    return $this->createRootConfigCopy($objectType, $objectId, $newValue);
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
	 * Получить объект, используемый в качестве значения настройки
	 * 
	 * @return CActiveRecord
	 */
	public function getValueObject()
	{
	    if ( ! is_subclass_of($this->valuetype, 'CActiveRecord') OR ! $this->valueid )
	    {// значение настройки не связано с какой-либо AR-моделью
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
	 * Эта функция получает только значения: если вам нужны модели целиком 
	 * используйте $this->defaultListItems
	 * 
	 * @param bool $removeSelected - что делать с теми элементами, которые уже выбраны? 
	 *                               * true   - ничего не делать
	 *                               * false  - убрать из списка
	 * @return array
	 * 
	 * @todo оптимизировать способ удаления выбранных элементов из списка стандартных
	 */
	public function getDefaultOptions($removeSelected=true)
	{
	    $defaultOptions = CHtml::listData($this->defaultListItems, 'id', 'title');
	    if ( ! $removeSelected )
	    {
	        return $defaultOptions;
	    }
	    foreach ( $this->selectedListItems as $selectedItem )
	    {
	        if ( isset($defaultOptions[$selectedItem->parentid]) )
	        {// выбранные значения настроек всегда будут ссылаться на стандартные в качестве родительских
	            unset($defaultOptions[$selectedItem->parentid]);
	        }
	    }
	    return $defaultOptions;
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
	public function getUserOptions($removeSelected=true)
	{
	    return CHtml::listData($this->userListItems, 'id', 'title');
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
	    return CHtml::listData($this->selectedListItems, 'id', 'title');
	}
	
	/**
	 * Именованая группа условий: все настройки, использующие этот список вариантов значений
	 *
	 * @param  int|array|EasyList $defaultList - id списка стандартных значений настройки, сама модель
	 *                                           такого списка (EasyList), или массив id таких списков
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Config
	 */
	public function forDefaultList($defaultList, $operation='AND')
	{
	    if ( $defaultList instanceof EasyList )
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
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Config
	 */
	public function forUserList($userList, $operation='AND')
	{
	    if ( $userList instanceof EasyList )
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
	 * @param  string $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
	 * @param  string $operation   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
	 * @param  string $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
	 * @param  string $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
	 * @param  string $operation   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
    	            'scopes'   => array(
        	            'withValue' => array($value),
                    ),
                ),
	        );
	    }else
	    {// настройка с одиночным выбором всегда ссылается на значение в поле другой модели
	        // создаем условие поиска по значению связанного поля
	        $with = array(
	            'selectedListItem' => array(
	                'select'    => false,
	                'joinType'  => 'INNER JOIN',
                    'scopes'    => array(
        	            'withValue' => array($value),
                    ),
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
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Config
	 */
	public function withAnySelectedOption($option, $operation='AND')
	{
	    if ( $option instanceof EasyListItem )
	    {// передана одна модель с вариантом значения
            $optionId = $option->id;
	    }else
	    {
	        $optionId = $option;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'selectedListItems' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'withItemId' => array($optionId),
	            ),
	        ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * @todo Получить все настройки в которых используется (выбран) переданый вариант 
	 * стандартного значения настройки (проверяется только стандартный список вариантов)
	 *
	 * @param  int|array|EasyListItem $option
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Config
	 */
	/*public function withSelectedDefaultOption($option, $operation='AND')
	{
	    
	}*/
	
	/**
	 * @todo Получить все настройки в которых используется (выбран) переданый вариант стандартного
	 * значения настройки (как из стандартного списка вариантов так и из пользовательского)
	 *
	 * @param  int|array|EasyListItem $option - 
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Config
	 */
	/*public function withEverySelectedOption($option, $operation='AND')
	{
        
	}*/
	
	/**
	 * (alias) Получить все настройки кроме тех, в которых используется (выбран) 
	 * переданый вариант стандартного значения настройки
	 * (как из стандартного списка вариантов так и из пользовательского)
	 *
	 * @param  int|array|EasyListItem $option - 
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Config
	 */
	public function exceptSelectedOption($option, $operation='AND')
	{
	    return $this->exceptAnySelectedOption($option, $operation);
	}
	
	/**
	 * Получить все настройки кроме тех, в которых используется (выбран) 
	 * переданый вариант стандартного значения настройки
	 * (как из стандартного списка вариантов так и из пользовательского)
	 *
	 * @param  int|array|EasyListItem $option - 
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Config
	 */
	public function exceptAnySelectedOption($option, $operation='AND')
	{
	    if ( ! $option )
	    {// условие не используется
            return $this;
	    }
	    if ( $option instanceof EasyListItem )
	    {
	        $optionId = $option->id;
	    }else
	    {
	        $optionId = $option;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'selectedListItems' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'exceptItemId' => array($optionId),
	            ),
	        ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	     
	    return $this;
	}
	
	/**
	 * Определить является ли переданный вариант значения настройки ее стандартным значением
	 * (или ссылкой на такое значение)
	 *
	 * @param  EasyListItem $option
	 * @return bool
	 */
	public function hasDefaultOption(EasyListItem $option)
	{
        if ( $option->easylistid == $this->easylistid )
        {// значение содержится в списке стандартных
            return true;
        }
        if ( $option->parentItem AND $option->parentItem->easylistid == $this->easylistid )
        {// является ссылкой на стандартное значение
            return true;
        }
        return false;
	}
	
	/**
	 * Определить является ли переданный вариант значения пользователем - то есть добавлен ли он
	 * пользователем в список значений настройки когда стандартного списка не хватило
	 * (или ялвляется ли он ссылкой на такое значение)
	 *
	 * @param  EasyListItem $option
	 * @return bool
	 */
	public function hasUserOption(EasyListItem $option)
	{
	    if ( $option->easylistid == $this->userlistid )
	    {// значение содержится в списке пользовательских
            return true;
	    }
	    if ( $option->parentItem AND $option->parentItem->easylistid == $this->userlistid )
	    {// является ссылкой на пользовательское значение
            return true;
	    }
	    return false;
	}
	
	/**
	 * Определить является ли переданный вариант значения настройки ее выбранным значением
	 *
	 * @param  EasyListItem $option
	 * @return bool
	 */
	public function hasSelectedOption(EasyListItem $option)
	{
	    if ( $this->isMultiple() )
	    {// для настроек с множественным выбором: ищем значение в списке
	        if ( $option->easylistid == $this->valueid )
	        {// значение содержится в списке выбранных
                return true;
	        }
	        if ( $option->parentItem AND $option->parentItem->easylistid == $this->valueid )
	        {// является ссылкой на стандартное значение
                return true;
	        }
	    }elseif ( $this->valuetype === 'EasyListItem' )
	    {// для настроек с одним значением: ищем значение в самой настройке
	        if ( $option->id == $this->valueid )
	        {// значение содержится в списке выбранных
                return true;
	        }
	        if ( $option->parentid AND $option->parentid == $this->valueid )
	        {// является ссылкой на выбранное значение
                return true;
	        }
	    }
	    return false;
	}
	
	/**
	 * Восстановить изначальное значение настройки
	 * 
	 * @return bool
	 */
	public function restoreDefault()
	{
	    if ( $this->isRoot() OR $this->isSystem() )
	    {// корневые и системные настройки не могут быть сброшены
	        return false;
	    }
        return $this->delete();
	}
	
	/**
	 * Создать новую настройку из данных корневой настройки
	 * 
	 * @param  string $objectType - тип модели для которой редактируется настройка
	 * @param  string $objectId   - id модели для которой редактируется настройка
	 * @param  string $newValue
	 * @return Config
	 * 
	 * @todo принудительно создавать список для введенных значений если maxvalues != 1
	 *       но в корневой настройке список для введенных значений отсутствует
	 */
	protected function createRootConfigCopy($objectType, $objectId, $newValue=null)
	{
	    // получаем корневую настройку и используем ее как шаблон при создании новой
	    $rootConfig = $this->getRootConfig();
	    // поля, которые переносятся без изменения
	    $fields     = array('allowuservalues', 'name', 'title', 'description', 'type',
            'minvalues', 'maxvalues', 'valuetype', 'schemaid');
	    
	    // создаем новую модель настройки
	    $newConfig = new Config();
	    if ( $rootConfig->selectedList )
	    {// копируем список значений настройки (если это настройка с множественным выбором)
	        $newSelectedList       = $rootConfig->selectedList->createCopy();
	        $newConfig->valueid    = $newSelectedList->id;
	        $newConfig->valuefield = 'listItems';
	    }else
	    {// переносим обычные значения простым присваиванием
	        $fields[] = 'valuefield';
	        $fields[] = 'valueid';
	    }
	    if ( $rootConfig->defaultList )
	    {// список значений по умолчанию всегда общий для всей линии наследования настроек  
	        $newConfig->easylistid = $rootConfig->defaultList->id;
	    }
	    if ( $rootConfig->allowuservalues )
	    {// для настройки разрешен ввод пользовательских значений - создадим список для них
	        // этот список не копируется из корневой настройки чтобы не смешивать 
	        // значения введенные для разных моделей
	        $userList       = new EasyList();
	        $userList->name = 'Список пользовательских значений для настройки "'.$rootConfig->title.'"';
	        $userList->triggerupdate  = EasyList::TRIGGER_MANUAL;
	        $userList->triggercleanup = EasyList::TRIGGER_MANUAL;
	        $userList->unique         = 1;
	        // сохраняем список
	        if ( ! $userList->save() )
	        {// @todo прописать действия при ошибке сохранения
	            $newConfig->userlistid = 0;
	        }
	        $newConfig->userlistid = $userList->id;
	    }
	    // запоминаем из какой модели былап создана новая настройка
	    $newConfig->parentid   = $rootConfig->id;
	    // прикрепляем новую настройку к нужной модели
	    $newConfig->objecttype = $objectType;
	    $newConfig->objectid   = $objectId;
	    // переносим остальные значения из корневой настройки
	    $newConfig->attributes = $rootConfig->getAttributes($fields);
	    // сохраняем настройку
	    if ( ! $newConfig->save() )
	    {// @todo обработать ошибку сохранения
	        return false;
	    }
	    return $newConfig;
	}
	
	/**
	 * Скопировать значения настройки из стандартного объекта в текущий перед изменением
	 * чтобы не задеть настройки по умолчанию 
	 * 
	 * @return bool
	 * 
	 * @deprecated
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
	 * @return int - id созданного списка
	 * 
	 * @deprecated
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
}