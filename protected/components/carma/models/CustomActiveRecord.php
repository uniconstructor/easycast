<?php

/**
 * Customizable Active Record class
 * 
 * @param string $arClassTable
 * 
 * @todo getTableSchema() для labels
 * @todo получать метаданные из модуля
 * @todo кешировать метаданные AR-записей
 * @todo namespaces
 */
class CustomActiveRecord extends CActiveRecord
{
    /**
     * @var array - custom AR class metadata (from {{ar_models}} table)
     */
    protected static $arModels    = array();
    /**
     * @var array 
     */
    protected static $arMeta      = array();
    /**
     * @var array - общий массив метаданных для всех классов CustomActiveRecord
     */
    protected static $armd        = array();
    /**
     * @var array - подключенные поведения модели
     */
    protected static $arBehaviors = array();
    /**
     * @var array - список правил проверки (для метода rules() в классе Active Record)
     */
    protected static $arRules     = array();
    /**
     * @var string
     */
    protected static $arTablePrefix = 'ar_';
    /**
     * @var string - класс, используемый для сбора метаданных
     */
    protected $arMetaDataClass     = 'CActiveRecordMetaData';
    /**
     * @var string
     */
    protected $arTranslationPrefix = 'app.modules.carma.models.';
    
    /**
     * 
     * @param type $scenario
     */
    public function __construct($scenario='insert')
    {
        if ( self::$arTablePrefix === null )
        {// получаем настройки AR-класса из модуля
            self::$arTablePrefix = Yii::app()->getComponent('carma')->arTablePrefix;
        }
        parent::__construct($scenario);
    }
    
    /**
     * @see parent::init()
     * 
     * @todo подключить behaviors из базы
     * @todo подключить обработчики событий из базы
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Определяет таблицу в зависимости от того какой AR-класс сейчас используется
     * 
     * @return string
     * 
     * @throws CException - если таблицу невозможно определить то будет вызвано исключение
     */
    public function tableName()
    {
        return $this->getArClassTable();
    }
    
    /**
     * @see parent::rules()
     */
    public function rules()
    {
        $rules = CMap::mergeArray(parent::rules(), $this->createColumnArRulels());
        return CMap::mergeArray($rules, $this->loadArRules());
    }

    /**
     * @see parent::getMetaData()
     */
    public function getMetaData()
    {
        $arClass = get_called_class();
        //$arClass = $this->arClassName;
		if ( ! array_key_exists($arClass, self::$armd) )
		{
            // получаем основные метаданные
            $activeMd = new CActiveRecordMetaData($this);
            // preventing recursive invokes of {@link getMetaData()} via {@link __get()}
            self::$armd[$arClass] = null;
			self::$armd[$arClass] = $activeMd;
		}
		return self::$armd[$arClass];
    }
    
    /**
     * @see parent::refreshMetaData()
     */
    public function refreshMetaData()
    {
        self::$arMeta   = null;
        self::$arModels = null;
        self::$armd     = null;
        self::$arMeta   = array();
        self::$arModels = array();
        self::$armd     = array();
        
        parent::refreshMetaData();
    }

    /**
     * @see parent::relations()
     * @return array
     * 
     * @todo прописать логику на случай наложения связей
     */
    public function relations()
    {
        $relations = parent::relations();
        $relations['arAttributes'] = array(self::HAS_MANY, 'ArModelAttribute', 'objectid', 
            'condition' => "`arAttributes`.`modelid`={$this->getArClassId()} OR (`arAttributes`.`objectid` = 0 AND `arAttributes`.`modelid`={$this->getArClassId()})",
        );
        return CMap::mergeArray($relations, $this->loadArRelations());
    }
    
    /**
     * @see parent::getActiveRelation()
     * 
     * @todo удалить если не используется
     */
    /*public function getActiveRelation($name)
	{
		return isset($this->getMetaData()->relations[$name]) ? $this->getMetaData()->relations[$name] : null;
	}*/
    
    /**
     * @see parent::attributeLabels()
     */
    public function attributeLabels()
    {
        $labels     = array();
        $attributes = $this->attributeNames();
        foreach ( $attributes as $name => $type )
        {
            $labels[$name] = $this->generateAttributeLabel($name);
        }
        return $labels;
    }
    
    /**
     * @see parent::behaviors()
     * 
     * @todo продумать случаи наложения подключаемых классов поведения
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // все модели, независимо от класса хранят информацию о дате создания и изменения объекта
        $behaviors['ecTimeStampBehavior'] = array(
            'class' => 'application.behaviors.EcTimestampBehavior',
        );
        return CMap::mergeArray($behaviors, $this->loadArBehaviors());
    }
    
    /**
     * @see parent::generateAttributeLabel()
     */
    public function generateAttributeLabel($attribute)
    {
        return $this->getTranslatedArLabel($attribute);
    }
    
    /**
     * Возвращает переведенное название модели
     * 
     * @param string $arClass - пользовательский или служебный AR-класс для которого получается информация
     * @return string
     */
    public function getArClassTitle($arClass=null)
    {
        return $this->getTranslatedArMeta('model_title', $arClass);
    }
    
    /**
     * Возвращает переведенное описание модели
     * 
     * @param string $arClass - пользовательский или служебный AR-класс для которого получается информация
     * @return string
     */
    public function getArClassDescription($arClass=null)
    {
        return $this->getTranslatedArMeta('model_description', $arClass);
    }
    
    /**
     * Получить id класса AR-модели в служебной таблице плагина
     * 
     * @param string $arClass - пользовательский или служебный AR-класс для которого получается информация
     * @return string
     */
    public function getArClassId($arClass=null)
    {
        return $this->loadArInfo('id', $arClass);
    }
    
    /**
     * Получить название имени класса загруженной AR-модели
     * 
     * @return string
     */
    public function getArClassName()
    {
        return get_called_class();
    }
    
    /**
     * Получить название таблицы, подставив все префиксы
     * 
     * @param string $table - название таблицы без префиксов
     * @return string
     */
    public function getArClassTable($table=null)
    {
        if ( ! $table )
        {
            $table = $this->loadArInfo('table');
        }
        return '{{'.self::$arTablePrefix.$table.'}}';
    }
    
    /**
     * Получить категорию языковых строк из которой будеи извлекаться перевод
     * 
     * @param string $arClass - пользовательский или служебный AR-класс для которого получается информация
     * @return string - категория сообщений с переводом для этой модели
     */
    public function getArI18nCategory($arClass=null)
    {
        if ( ! $arClass )
        {
            $arClass = $this->getArClassName();
        }
        return $this->arTranslationPrefix.mb_strtolower($arClass);
    }
    
    /**
     * Получить переведенное название колонки таблицы/атрибута
     * 
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $type      - тип переводимых данных для атрибута
     * @param string $arClass   - пользовательский или служебный AR-класс для которого получается информация
     * @return string 
     */
    public function getTranslatedArAttribute($attribute, $type, $arClass=null)
    {
        return Yii::t($this->getArI18nCategory($arClass), $this->getI18nTemplate($type, $attribute), array(), 'dbMessages');
    }
    
    /**
     * Получить переведенное название колонки таблицы/атрибута
     * 
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $type - 
     * @param string $arClass - пользовательский или служебный AR-класс для которого получается информация
     * @return string 
     * 
     * @todo дубль функции - удалить при рефакторинге
     */
    public function setTranslatedArAttribute($attribute, $translation, $type, $arClass=null, $language=null)
    {
        return $this->setI18nArAttributeTranslation($attribute, $translation, $type, $language, $arClass);
    }
    
    /**
     * Получить название название строки перевода для колонки таблицы/атрибута
     * 
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $arClass   - пользовательский или служебный AR-класс для которого получается информация
     * @return string 
     */
    public function hasI18nArAttributeMessage($attribute, $type, $arClass=null)
    {
        return (bool)$this->getI18nArAttributeMessageObject($attribute, $type, $arClass);
    }
    
    /**
     * Получить название название строки перевода для колонки таблицы/атрибута
     * 
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $arClass   - пользовательский или служебный AR-класс для которого получается информация
     * @return ArI18nMessage
     */
    public function getI18nArAttributeMessageObject($attribute, $type, $arClass=null)
    {
        if ( ! $arClass )
        {
            $arClass = $this->getArClassName();
        }
        $attributes = array(
            'category' => $this->getArI18nCategory($arClass),
            'message'  => $this->getI18nTemplate($type, $attribute),
        );
        return ArI18nMessage::model()->findByAttributes($attributes);
    }
    
    /**
     * Проверить существование перевода на указанный язык для колонки таблицы/атрибута модели
     * 
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $language  - язык для которого проверяется наличие перевода
     * @param string $arClass   - пользовательский или служебный AR-класс для которого получается информация
     * @return bool
     */
    public function hasI18nArAttributeTranslation($attribute, $type, $language=null, $arClass=null)
    {
        return (bool)$this->getI18nArAttributeTranslationObject($attribute, $type, $language, $arClass);
    }
    
    /**
     * Получить объект хранящий строку перевода на указанный язык для колонки таблицы/атрибута модели
     * 
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $language  - язык для которого проверяется наличие перевода
     * @param string $arClass   - пользовательский или служебный AR-класс для которого получается информация
     * @return bool
     */
    public function getI18nArAttributeTranslationObject($attribute, $type='label', $language=null, $arClass=null)
    {
        if ( ! $language )
        {
            $language = Yii::app()->language;
        }
        if ( ! $message = $this->getI18nArAttributeMessageObject($attribute, $type, $arClass) )
        {
            return false;
        }
        $attributes = array(
            'id'       => $message['id'],
            'language' => $language,
        );
        return ArI18nTranslation::model()->findAllByAttributes($attributes);
    }
    
    /**
     * Добавить или обновить строку перевода на указанный язык для колонки таблицы/атрибута модели
     * 
     * @param string $attribute      - название атрибута модели для которого запрошен перевод
     * @param string $newTranslation - текст перевода для атрибута
     * @param string $type           - тип перевода
     * @param string $language       - язык для которого проверяется наличие перевода
     * @param string $arClass        - пользовательский или служебный AR-класс для которого получается информация
     * @return bool
     */
    public function setI18nArAttributeTranslation($attribute, $newTranslation, $type='label', $language=null, $arClass=null)
    {
        if ( ! $language )
        {
            $language = Yii::app()->language;
        }
        if ( ! $arClass )
        {
            $arClass = $this->getArClassName();
        }
        if ( ! $message = $this->getI18nArLabelMessageObject($attribute, $arClass) )
        {
            $message = new ArI18nTranslation();
            $message->category = $this->getArI18nCategory($arClass);
            $message->message  = $this->getI18nTemplate($type, $attribute);
            if ( ! $message->save() )
            {
                throw new CException('Unable to save translation template');
            }
        }
        if ( ! $translation = $this->getI18nArLabelTranslationObject($attribute, $language, $arClass) )
        {
            $translation = new ArI18nTranslation();
            $translation->id       = $message->id;
            $translation->language = $language;
        }
        $translation->translation = $newTranslation;
        if ( ! $translation->save() )
        {
            throw new CException("Unable to save label translation '{$message->message}' message for language '{$language}'");
        }
        return true;
    }
    
    /**
     * Получить переведенное название колонки таблицы/атрибута
     * 
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $arClass - пользовательский или служебный AR-класс для которого получается информация
     * @return string 
     */
    public function getTranslatedArHint($attribute, $arClass=null)
    {
        return Yii::t($this->getArI18nCategory($arClass), $this->getI18nTemplate('hint', $attribute), array(), 'dbMessages');
    }
    
    /**
     * Получить переведенное значение атрибута модели
     * Этот метод позволяет добавлять перевод к введенным пользовательским данным
     * 
     * @param int    $id        - id записи для которой извлекается перевод
     * @param string $attribute - название атрибута модели для которого запрошен перевод
     * @param string $arClass   - пользовательский или служебный AR-класс для которого получается информация
     * @return string 
     */
    public function getTranslatedArAttributeValue($id, $attribute, $arClass=null)
    {
        return Yii::t($this->getArI18nCategory($arClass), $this->getI18nTemplate('attribute_value', $attribute), array(), 'dbMessages');
    }
    
    /**
     * Получить переведенный фрагмент метаданных модели
     * 
     * @param string $name    - название элемента метаданных
     * @param string $arClass - пользовательский или служебный AR-класс для которого получается информация
     * @return string
     */
    public function getTranslatedArMeta($name, $arClass=null)
    {
        return Yii::t($this->getArI18nCategory($arClass), $this->getI18nTemplate('meta', $name), array(), 'dbMessages');
    }
    
    /**
     * получить шаблон строки перевода для атрибута модели
     * 
     * @param type $item - поле или атрибут модели для которого создается шаблон строки
     * @param type $type - тип шаблона для строки перевода (название поля, описание поля, метаданные и т. д.)
     * @return string
     */
    protected function getI18nTemplate($item, $type)
    {
        switch ( $type )
        {
            case 'label':            return "ar_label_{$item}";
            case 'meta':             return "ar_meta_{$item}";
            case 'hint':             return "ar_hint_{$item}";
            case 'attribute_value':  return "ar_attribute_{$type}_value_{$item}";
        }
        return 'ar_'.$type.'_'.$item;
    }
    
    /**
     * Настроить текущий AR-объект на работу с переданным классом записи: загрузить из базы метаданные,
     * обновить связи, набор полей и все остальные настраиваемые параметры класса ActiveRecord
     * 
     * @param  string $arClass
     * @param  string $key
     * @return array|string
     */
    protected function loadArInfo($key=null, $arClass=null)
    {
        if ( ! $arClass )
        {// по умолчанию настраиваем модель под тот класс экземпляр которого был вызван
            $arClass = get_called_class();
        }
        if ( in_array($arClass, array('CustomActiveRecord', 'CActiveRecord')) )
        {// неизвестно откуда брать метаданные
            throw new CException('Incorrect CustomActiveRecord call');
        }
        if ( ! isset(self::$arModels[$arClass]) )
        {// кеширование данных AR-записи
            $arData = $this->getDbConnection()->createCommand()->select()->from('{{ar_models}}')->
                where('model = :model', array(':model' => $arClass))->queryRow();
            if ( ! $arData )
            {// такая модель не используется модулем
                throw new CException("Model '{$arClass}' not exists in table '{$this->arClassTable}' ");
            }
            self::$arModels[$arClass] = $arData;
        }
        if ( $key )
        {// возвращаем один элемент метаданных
            if ( ! isset(self::$arModels[$arClass][$key]) )
            {
                throw new CException("No AR metadata with key '{$key}'");
            }
            return self::$arModels[$arClass][$key];
        }
        return self::$arModels[$arClass];
    }
    
    /**
     * Дополнить связи модели (relations) по данным из базы
     * 
     * @return array - custom relations, stored in DB
     * 
     * @todo реализовать остальные параметры для relations()
     * @todo убрать параметр $arClass
     */
    protected function loadArRelations()
    {
        // по умолчанию подгружаем связи текущей AR-модели
        $arClass = $this->getArClassName();
        if ( ! isset(self::$arMeta[$arClass]['relations']) )
        {// связей нет в кеше - тащим их из базы
            $relations = array();
            $table     = '{{'.self::$arTablePrefix.'relations}}';
            $modelId   = $this->loadArInfo('id');
            // извлекаем записи содержащие связи этой модели с другими
            $relData   = $this->getDbConnection()->createCommand()->select()->
                from($table)->where('`modelid` = :modelid', array(':modelid' => $modelId))->queryAll();
            foreach ( $relData as $item )
            {// из каждой записи делаем массив настроек для $this->relations
                $foreignKey = $item['fk0'];
                if ( ! $foreignKey )
                {
                    $foreignKey = array();
                    if ( $item['fk1'] AND $item['pk1'] )
                    {
                        $foreignKey[] = array($item['fk1'] => $item['pk1']);
                    }
                    if ( $item['fk2'] AND $item['pk2'] )
                    {
                        $foreignKey[] = array($item['fk2'] => $item['pk2']);
                    }
                    if ( $item['fkc1'] AND $item['pkc1'] )
                    {
                        $foreignKey[$item['fkc1']] = $item['pkc1'];
                    }
                    if ( $item['fkc2'] AND $item['pkc2'] )
                    {
                        $foreignKey[$item['fkc2']] = $item['pkc2'];
                    }
                    if ( ! $foreignKey )
                    {// не указан ни один из вариантов внешнего ключа
                        throw new CException('Error: foreign key not set fo relation "'.$item['name'].'" '
                            .' in model "'.$arClass.'"');
                    }
                }
                // внешний ключ настроен - создаем связь
                $relations[$item['name']] = array(
                    // тип связи
                    $item['type'],
                    // AR-класс модели с которой создается связь
                    $item['relatedmodel'], 
                    // первичный и вторичный ключ по которому устанавливается связь
                    $foreignKey,
                    // @todo alias
                    // @todo together
                    // @todo through
                    // @todo title
                    // @todo description
                );
                if ( isset($item['condition']) AND $item['condition'] )
                {// если для внешнего ключа есть дополнительные условия - добавим их
                    $relations[$item['name']]['condition'] = $item['condition'];
                }
            }
            self::$arMeta[$arClass]['relations'] = $relations;
        }
        return self::$arMeta[$arClass]['relations'];
    }
    
    /**
     * Загрузить дополнительные классы поведения из базы
     * 
     * @return array
     * 
     * @todo подстановка переменных в значения для config
     */
    protected function loadArBehaviors()
    {
        // TODO
        return array();
        /*
        $arClass = $this->loadArInfo('model');
        if ( ! isset(self::$arBehaviors[$arClass]) )
        {
            $behaviors = array();
            $table     = '{{'.self::$arTablePrefix.'behaviors}}';
            $modelId   = $this->loadArInfo('id');
            $behaviorList = $this->getDbConnection()->createCommand()->select()->
                from($table)->where('modelid = :modelid', array(':modelid' => $modelId))->queryAll();
            foreach ( $behaviorList as $item )
            {
                $behavior = array(
                    'class' => $item['class'],
                );
                $config = CJSON::decode($item['configdata']);
                if ( is_array($config) AND $config )
                {// @todo $key=>value + evaluateExpression($value)
                    $behavior = CMap::mergeArray($behavior, $config);
                }
                $behaviors[$item['name']] = $behavior;
                unset($behavior, $config);
            }
            self::$arBehaviors[$arClass] = $behaviors;
        }
        return self::$arBehaviors[$arClass];
        */
        
    }
    
    /**
     * Загрузить фильтры и валидаторы для проверки входных данных во всех полях модели
     * 
     * @return array
     * 
     * @todo брать список полей из отдельной таблицы, массивом а не строкой
     */
    protected function loadArRules()
    {
        $arClass = $this->loadArInfo('model');
        if ( ! isset(self::$arRules[$arClass]) )
        {
            $rules     = array();
            $table     = '{{'.self::$arTablePrefix.'rules}}';
            $modelId   = $this->loadArInfo('id');
            $rulesList = $this->getDbConnection()->createCommand()->select()->
                from($table)->where('modelid = :modelid', array(':modelid' => $modelId))->queryAll();
            foreach ( $rulesList as $item )
            {
                $rule = array(
                    // проверяемые фильтром поля, строкой через запятую
                    $item['attributes'],
                    $item['validator'],
                );
                if ( $item['on'] )
                {
                    $rule['on'] = $item['on'];
                }
                // дополнительные настройки фильтра
                $config = CJSON::decode($item['configdata']);
                if ( is_array($config) AND $config )
                {// @todo $key=>value + evaluateExpression($value)
                    $rule = CMap::mergeArray($rule, $config);
                }
                $rules[] = $rule;
                unset($rules, $config);
            }
            self::$arRules[$arClass] = $rules;
        }
        return self::$arRules[$arClass];
    }
    
    /**
     * Загрузить фрагмент метаданных AR-моделей
     * 
     * @param string $name - название фрагмента метаданных
     * @return array
     */
    protected function loadArMetaFragment($name)
    {
        $arClass = $this->getArClassName();
        $table   = $this->getArTableName($table=null);
        if ( ! isset(self::$arRules[$arClass]) )
        {
            $rules     = $this->createColumnArRulels();
            $table     = '{{'.self::$arTablePrefix.'rules}}';
            $modelId   = $this->loadArInfo('id');
            $rulesList = $this->getDbConnection()->createCommand()->select()->
                from($table)->where('modelid = :modelid', array(':modelid' => $modelId))->queryAll();
            foreach ( $rulesList as $item )
            {
                $rule = array(
                    // проверяемые фильтром поля, строкой через запятую
                    $item['attributes'],
                    $item['validator'],
                );
                if ( $item['on'] )
                {
                    $rule['on'] = $item['on'];
                }
                // дополнительные настройки фильтра
                $config = CJSON::decode($item['configdata']);
                if ( is_array($config) AND $config )
                {// @todo $key=>value + evaluateExpression($value)
                    $rule = CMap::mergeArray($rule, $config);
                }
                $rules[] = $rule;
                unset($rules, $config);
            }
            self::$arRules[$arClass] = $rules;
        }
        return self::$arRules[$arClass];
    }
    
    /**
     * Проверить, загружен ли такой фрагмент метаданных
     * 
     * @param string $name - 
     * @return bool
     */
    protected function arMetaFragmentIsLoaded($name)
    {
        switch ( $name )
        {
            
        }
    }
    
    /**
     * Создать набор правил по метаданным колонок таблиц
     * 
     * @return array - список правил валидации и фильтры
     */
    protected function createColumnArRulels()
    {
        $rules   = array();
        $filters = array();
        // создаем фильтры и валидаторы по метаданным колонок таблицы
        // при изменении структуры таблицы проверуи изменяются вместе с ней автоматически
        $columns = $this->getMetaData()->columns;
        // фильтры применяются ко всем колонкам таблицы кроме первичного ключа,
        // времени создания/изменения и статуса
        unset($columns[$this->getMetaData()->tableSchema->primaryKey]);
        unset($columns['timecreated']);
        unset($columns['timemodified']);
        unset($columns['status']);
        
        // перебираем оставшиеся колонки и добавляем нужные фильтры для каждой
        foreach ( $columns as $name => $columnData )
        {/* @var $columnData CMysqlColumnSchema */
            if ( mb_strstr($columnData->dbType, 'int') )
            {// только целые числа
                $filters['numerical'][$name] = $columnData;
                $filters['length'][$name]    = $columnData;
            }elseif ( mb_strstr($columnData->dbType, 'text') )
            {// очистка текста от XSS для любого текста
                $filters['purify'][$name] = $columnData;
                $filters['trim'][$name]   = $columnData;
            }elseif ( mb_strstr($columnData->dbType, 'varchar') )
            {// очистка строки от потенциального XSS
                $filters['trim'][$name]   = $columnData;
                $filters['purify'][$name] = $columnData;
                $filters['length'][$name] = $columnData;
            }
        }
        foreach ( $filters as $name => $attributes )
        {// для каждого фильтра создаем список правил
            $attributeNames = implode(',', array_keys($attributes));
            switch ( $name )
            {
                case 'trim': 
                    $rules[] = array($attributeNames, 'filter', 'filter' => 'trim');
                break;
                case 'numerical':
                    $rules[] = array($attributeNames, 'numerical', 'integerOnly' => true);
                break;
                case 'purify':    
                    $rules[] = array($attributeNames, 'filter', 'filter' => array(Yii::app()->getComponent('htmlPurifier'), 'purify'));
                break;
                case 'length':
                    foreach ( $attributes as $name => $attribute )
                    {// правила длины назначаются индивидуально для каждого поля
                        $rules[] = array($name, 'length', 'max' => $attribute->size); break;
                    }
                break;
            }
            
        }
        return $rules;
    }
}