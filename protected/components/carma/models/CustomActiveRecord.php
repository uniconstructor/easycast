<?php

/**
 * Customizable Active Record class
 * 
 * @todo getTableSchema() для labels
 * @todo получать метаданные из модуля
 * @todo кешировать метаданные AR-записей
 * @todo getCurrentArClass()
 * @todo стандартный метод получения названия таблицы {{ar_TABLENAME}}
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
    protected static $arTablePrefix;
    /**
     * @var string - класс, используемый для сбора метаданных
     */
    protected $arMetaDataClass = 'CActiveRecordMetaData';
    
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
        return '{{'.self::$arTablePrefix.$this->loadArInfo()['table'].'}}';
    }
    
    /**
     * @see parent::rules()
     */
    public function rules()
    {
        $rules = parent::rules();
        return CMap::mergeArray($rules, $this->loadArRules());
    }

    /**
     * @see parent::getMetaData()
     */
    public function getMetaData()
    {
        $arClass = get_called_class();
		if ( ! array_key_exists($arClass, self::$armd) )
		{
            // получаем основные метаданные
            $activeMd = new CActiveRecordMetaData($this);
            $activeMd->relations = $this->loadArRelations($arClass);
            //CVarDumper::dump($activeMd, 10, true);die;
            // preventing recursive invokes of {@link getMetaData()} via {@link __get()}
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
     * Динамически дополнить связи модели (relations)
     * 
     * @param  string $arClass - AR class name
     * @return array - custom relations, stored in DB
     * 
     * @todo остальные параметры для relations()
     */
    protected function loadArRelations($arClass=null)
    {
        if ( $arClass === null )
        {// по умолчанию подгружаем связи текущей AR-модели
            $arClass = $this->loadArInfo(get_called_class(), 'model');
        }
        if ( ! isset(self::$arMeta[$arClass]['relations']) )
        {// связей нет в кеше - тащим их из базы
            $relations = array();
            $table     = '{{'.self::$arTablePrefix.'relations}}';
            $modelId   = $this->loadArInfo(get_called_class(), 'id');
            // извлекаем записи содержащие связи этой модели с другими
            $relData   = $this->getDbConnection()->createCommand()->select()->
                from($table)->where('modelid = :modelid', array(':modelid' => $modelId))->queryAll();
            foreach ( $relData as $item )
            {// из каждой записи делаем массив настроек для $this->relations
                $relations[$item['name']] = array(
                    // тип связи
                    $item['type'],
                    // AR-класс модели с которой создается связь
                    $item['relatedmodel'], 
                    // первичный и вторичный ключ по которому устанавливается связь
                    $item['fkdata'],
                    // @todo condition
                    // @todo alias
                    // @todo together
                    // @todo through
                    // @todo title
                    // @todo description
                );
            }
            self::$arMeta[$arClass]['relations'] = $relations;
        }
        return self::$arMeta[$arClass]['relations'];
    }
    
    /**
     * @see parent::relations()
     * @return array
     * 
     * @todo прописать логику на случай наложения связей
     */
    public function relations()
    {
        $relations      = parent::relations();
        $curomRelations = $this->loadArRelations();
        return CMap::mergeArray($relations, $curomRelations);
    }
    
    /**
     * @see parent::attributeLabels()
     * @return array
     */
    public function attributeLabels()
    {
        $labels     = array();
        $arClass    = $this->loadArInfo(get_called_class(), 'model');
        $attributes = $this->attributeNames();
        foreach ( $attributes as $name => $type )
        {
            $labels[$name] = Yii::t("carma.models", "{$arClass}.{$name}.label");
        }
        return $labels;
    }
    
    /**
     * @see parent::generateAttributeLabel()
     */
    public function generateAttributeLabel($name)
    {
        $arClass = $this->loadArInfo(get_called_class(), 'model');
        return Yii::t("carma.models", "{$arClass}.{$name}.label");
    }
    
    /**
     * Возвращает переведенное название модели
     * 
     * @return string
     */
    public function getTitle()
    {
        if ( $this->hasAttribute('title') )
        {
            return $this->title;
        }
        // возвращяем автоматический перевод только если в таблице не предусмотрена такая колонка
        $arClass = $this->loadArInfo(get_called_class(), 'model');
        return Yii::t("carma.models", "{$arClass}.title");
    }
    
    /**
     * Возвращает переведенное описание модели
     * 
     * @return string
     */
    public function getDescription()
    {
        if ( $this->hasAttribute('description') )
        {
            return $this->title;
        }
        // возвращяем автоматический перевод только если в таблице не предусмотрена такая колонка
        $arClass = $this->loadArInfo(get_called_class(), 'model');
        return Yii::t("carma.models", "{$arClass}.description");
    }

    /**
     * @see parent::behaviors()
     * 
     * @todo продумать случаи наложения подключаемых классов поведения
     */
    public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(), $this->loadArBehaviors());
    }

    /**
     * Настроить текущий AR-объект на работу с переданным классом записи: загрузить из базы метаданные,
     * обновить связи, набор полей и все остальные настраиваемые параметры класса ActiveRecord
     * 
     * @param  string $arClass
     * @param  string $key
     * @return array|string
     * 
     * @todo использование $key
     */
    protected function loadArInfo($arClass=null, $key=null)
    {
        if ( ! $arClass )
        {// по умолчанию настраиваем модель под тот класс экземпляр которого был вызван
            $arClass = get_called_class();
        }
        if ( $arClass === 'CustomActiveRecord' OR $arClass === 'CActiveRecord' )
        {// неизвестно откуда брать метаданные
            throw new CException('Incorrect CustomActiveRecord call');
        }
        if ( ! isset(self::$arModels[$arClass]) )
        {// кеширование данных AR-записи
            $table  = '{{'.self::$arTablePrefix.'models}}';
            $arData = $this->getDbConnection()->createCommand()->select()->
                from($table)->where('model = :model', array(':model' => $arClass))->queryRow();
            if ( ! $arData )
            {// такая модель не используется модулем
                throw new CException('Model '.$arClass.' not exists in table "models"');
            }
            self::$arModels[$arClass] = $arData;
        }
        if ( $key )
        {// @todo проверить существование $key
            return self::$arModels[$arClass][$key];
        }
        return self::$arModels[$arClass];
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
        // FIXME
        return array();
        $arClass = $this->loadArInfo(get_called_class(), 'model');
        if ( ! isset(self::$arBehaviors[$arClass]) )
        {
            $behaviors = array();
            $table     = '{{'.self::$arTablePrefix.'behaviors}}';
            $modelId   = $this->loadArInfo(get_called_class(), 'id');
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
        $arClass = $this->loadArInfo(get_called_class(), 'model');
        if ( ! isset(self::$arRules[$arClass]) )
        {
            $rules     = array();
            $table     = '{{'.self::$arTablePrefix.'rules}}';
            $modelId   = $this->loadArInfo(get_called_class(), 'id');
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
}