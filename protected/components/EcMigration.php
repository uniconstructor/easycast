<?php

/**
 * Наш собственный класс для миграций
 * Добавляет в стандартную миграцию дополнительные методы, 
 * облегчающие работу с таблицами и перенос данных
 * 
 * Все создаваемые этой миграцией таблицы будут использовать движок InnoDB, 
 * так как такие таблицы в Amazon RDS можно восстановить из резервной копии
 * к любой точке во времени (restore to point in time)
 * 
 * Кодировка всех таблиц (да и вообще всех данных в системе) всегда должна быть utf8
 * Используемое сравнение (collation) для всех таблиц базы: utf8_inicode_ci 
 * 
 * @todo не разрешать создание 2 настроек с одинаковым именем для одной модели
 * 
 * Часто используемые настройки: 
 * systemNotificationsList - список стандартных оповещений системы
 * 
 */
class EcMigration extends CDbMigration
{
    /**
     * @var string - Настройки для всех создаваемых этой миграцией таблиц
     */
    const EC_MYSQL_OPTIONS = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
    
    /**
     * @var array - системные и корневые настройки, кэшируются здесь чтобы служить
     *              образцом для создания настроек для обычных записей
     *              (для поиска по id настройки)
     */
    protected $_configCache = array();
    /**
     * @var array - системные и корневые настройки, кэшируются здесь чтобы служить
     *              образцом для создания настроек для обычных записей
     *              (для поиска по названию настройки в модели)
     */
    protected $_modelsConfigCache = array();
    /**
     * @var array - списки
     */
    protected $_listsCache = array();
    
    /**
     * @see CDbMigration::createTable()
     */
    public function createTable($table, $columns, $options=self::EC_MYSQL_OPTIONS)
    {
        return parent::createTable($table, $columns, $options);
    }
    
    /**
     * Создать настройку
     * Кэширует значения корневых и системных настроек
     * 
     * @param  array $configData - изначальные значения записи
     * @return int - id вставленной записи
     * 
     * @todo проверки перед вставкой
     * @todo проверки результата вставки
     */
    public function createConfig($configData=array())
    {
        $template = array(
            'timecreated' => time(),
        );
        if ( isset($configData['type']) AND in_array($configData['type'], array('multiselect', 'checkboxlist')) )
        {
            $template['maxvalues']  = 0;
            $template['valuetype']  = 'EasyList';
            $template['valuefield'] = 'listItems';
        }
        if ( isset($configData['type']) AND in_array($configData['type'], array('select', 'radio')) )
        {
            $template['maxvalues']  = 1;
            $template['valuetype']  = 'EasyListItem';
            $template['valuefield'] = 'value';
        }
        if ( isset($configData['objecttype']) AND $configData['objecttype'] === 'system' )
        {
            $template['objectid'] = 0;
        }
        // составляем настройку из шаблона и переданных данных
        $config = CMap::mergeArray($template, $configData);
        // сохраняем настройку
        $this->insert("{{config}}", $config);
        // получаем id новой настройки
        $config['id'] = $this->dbConnection->lastInsertID;
        
        if ( ! $config['objectid'] )
        {// кешируем настройку если она корневая или системная
            $this->_configCache[$config['id']] = $config;
            $this->_modelsConfigCache[$config['objecttype']][$config['name']]["0"] = $config;
        }
        return $this->dbConnection->lastInsertID;
    }
    
    /**
     * Создать корневую настройку для модели и прикрепить ее к каждой модели в таблице
     * 
     * @param  array  $configData - изначальные значения записи
     * @param  string $modelTable
     * @param  bool   $copyValues - копировать ли ссылку на значение корневой настройки 
     *                              во все дочерние в качестве изначального значения?
     * @return bool
     */
    public function createRootConfig($configData, $modelTable, $copyValues=false)
    {
        $rootConfigId   = $this->createConfig($configData);
        $rootConfigData = $this->loadConfigDataById($rootConfigId);
        unset($rootConfigData['id']);
        
        // созданную настройку прикрепляем к каждой записи
        $records = $this->dbConnection->createCommand()->select('id')->
            from($modelTable)->queryAll();
        foreach ( $records as $record )
        {
            $recordConfig = array(
                'objectid' => $record['id'],
                'parentid' => $rootConfigId,
            );
            if ( ! $copyValues )
            {// изначально создаем все новые настройки пустыми (незаполненными)
                unset($recordConfig['valuetype']);
                unset($recordConfig['valuefield']);
                unset($recordConfig['valueid']);
            }
            $recordConfig = CMap::mergeArray($rootConfigData, $recordConfig);
            $this->createConfig($recordConfig);
        }
        return $rootConfigId;
    }
    
    /**
     * Создать настройку из уже существующей/корневой
     * 
     * @param  int   $parentId  - id настройки-шаблона
     * @param  array $newValues - значения, отличающиеся от шаблона
     * @return int - id вставленной записи
     */
    public function duplicateConfig($parentId, $newValues=array())
    {
        $parentConfig = $this->loadConfigDataById($parentId);
        $config       = CMap::mergeArray($parentConfig, $newValues);
        
        return $this->createConfig($config);
    }
    
    /**
     * Создать список и заполнить его значениями, запомним все джанные в кеш
     * 
     * @param  string $cacheName - название списка, чтобы его можно было найти в кеше по имени
     * @param  array  $listData - данные для нового списка
     * @param  array  $items - массив элементов для заполнения списка
     * @return int - id созданного списка
     */
    public function createList($listData, $cacheName=null, $items=array())
    {
        $template = array(
            'itemtype'      => 'EasyListItem',
            'triggerupdate' => 'manual',
            'unique'        => 1,
            'timecreated'   => time(),
        );
        // составляем список из шаблона и переданных данных
        $list = CMap::mergeArray($template, $listData);
        // сохраняем список
        $this->insert("{{easy_lists}}", $list);
        // получаем id созданного списка
        $list['id'] = $this->dbConnection->lastInsertID;
        // кешируем созданное значение
        $this->_listsCache[$list['id']] = $list;
        if ( $cacheName )
        {
            $this->_listsCache[$cacheName] = $list;
        }
        if ( ! $items )
        {// список пустой, элементы создавать не надо
            return $list['id'];
        }
        
        $sortOrder = 1;
        // шаблон для элементов этого списка
        $itemTemplate = array(
            'easylistid'  => $list['id'],
            'timecreated' => time(),
            'objecttype'  => 'EasyListItem',
            'objectfield' => 'value',
            'objectid'    => 0,
        );
        foreach ( $items as $value => $data )
        {// заполняем список значениями
            if ( ! is_array($data) )
            {
                $data = array(
                    'name'  => $data,
                    'value' => $value,
                );
            }
            $data['sortorder'] = $sortOrder++;
            
            // создаем элемент списка из шаблона и переданных данных
            $item = CMap::mergeArray($itemTemplate, $data);
            // сохраняем элемент списка / получаем id созданного элемента
            $item['id'] = $this->createListItem($item);
            
            if ( $item['objecttype'] == 'EasyListItem' AND $item['objectfield'] == 'value' AND ! $item['objectid'] )
            {// стандартные элементы ссылаются на себя за значением - обновим созданную запись
                $item['objectid'] = $item['id'];
                $this->update("{{easy_list_items}}", array('objectid' => $item['id']), 'id='.$item['id']);
            }
        }
        return $list['id'];
    }
    
    /**
     * 
     * @param array $itemData
     * @return int
     */
    public function createListItem($itemData)
    {
        // шаблон для элементов этого списка
        $itemTemplate = array(
            'timecreated' => time(),
            'objecttype'  => 'EasyListItem',
            'objectfield' => 'value',
            'objectid'    => 0,
        );
        $item = CMap::mergeArray($itemTemplate, $itemData);
        
        if ( ! isset($item['easylistid']) OR ! $item['easylistid'] )
        {// лучше список по умолчанию чем вообще без списка
            $item['easylistid'] = (int)$this->getDefaultListId();
        }
        // сохраняем элемент списка
        $this->insert("{{easy_list_items}}", $item);
        // получаем id созданного элемента
        return $this->dbConnection->lastInsertID;
    }
    
    /**
     * Создать элемент списка, который хранит данные настройки
     * 
     * @return int
     * 
     * @todo автоматически создавать настройку self::DEFAULT_LIST_CONFIG_NAME
     */
    public function createDataItem($model, $value=null, $name=null, $listId=0, $description=null)
    {
        if ( ! $listId )
        {
            $listId = (int)$this->getDefaultListId($model);
        }
        // создаем и сохраняем элемент
        $itemData = array(
            'easylistid'  => $listId,
            'name'        => $name,
            'value'       => $value,
            'description' => $description,
        );
        return $this->createListItem($itemData);
    }
    
    /**
     * Скопировать элементы из одного списка в другой
     * 
     * @param int    $fromListId
     * @param int    $toListId
     * @param array  $excludeIds - исключить из копирования указанные элементы
     * @param string $asLink - скопировать элементы как "ссылки" - каждый скопированный элемент
     *                         будет помнить о своем оригинале
     * @return void
     * 
     * @todo проверять существование второго списка, и если его нет - выдавать ошибку
     */
    public function copyListItems($fromListId, $toListId, $excludeIds=array(), $asLink=true)
    {
        $table = "{{easy_list_items}}";
        $items = $this->dbConnection->createCommand()->select('*')->
            from($table)->where('easylistid='.$fromListId)->queryAll();
        
        $sortOrder = 1;
        foreach ( $items as $item )
        {// копируем каждый элемент списка
            $item['easylistid']   = $toListId;
            $item['valueid']      = $item['id'];
            $item['timecreated']  = time();
            $item['timemodified'] = time();
            $item['sortorder']    = $sortOrder++;;
            unset($item['id']);
            
            $this->insert($table, $item);
        }
        return true;
    }
    
    /**
     * Получить данные шаблона настройки (как правило это корневая или стстемная настройка)
     * 
     * @param  int $configId - id настройке в таблице {{config}}
     * @return array
     */
    protected function loadConfigDataById($configId)
    {
        if ( isset($this->_configCache[$configId]) )
        {// загружаем настройку из кэша если есть
            $config = $this->_configCache[$configId];
        }else
        {// если в кэше нет - достаем из базы и кэшируем
            $config = $this->dbConnection->createCommand()->select('*')->
                from('{{config}}')->where("id={$configId}")->queryRow();
            if ( ! $config )
            {
                throw new CException('В базе нет настройки с таким id');
            }
            $this->_configCache[$config['id']] = $config;
            $this->_modelsConfigCache[$config['objecttype']][$config['name']][$config['objectid']] = $config;
        }
        return $config;
    }
    
    /**
     * Получить данные шаблона настройки (как правило это корневая или стстемная настройка)
     * 
     * @param  string $objectType - класс модели к которой прикреплена настройка 
     * @param  string $configName - служебное название настройки внутри модели
     * @param  int    $objectId   - id модели
     * @return array
     */
    protected function loadModelConfig($objectType, $configName, $objectId=0)
    {
        if ( isset($this->_modelsConfigCache[$objectType][$objectId][$configName]) )
        {// загружаем настройку из кэша если есть
            $config = $this->_modelsConfigCache[$objectType][$objectId][$configName];
        }else
        {// если в кэше нет - достаем из базы и кэшируем
            $config = $this->dbConnection->createCommand()->select('*')->
                from('{{config}}')->queryRow();
            if ( ! $config )
            {
                throw new CException('В базе нет настройки с таким id');
            }
            $this->_modelsConfigCache[$objectType][$objectId][$configName] = $config;
        }
        return $config;
    }
    
    /**
     * 
     * @param string $cacheName
     * @return array
     */
    protected function loadListByName($cacheName)
    {
        if ( isset($this->_listsCache[$cacheName]) )
        {
            return $this->_listsCache[$cacheName];
        }
        return null;
    }
    
    /**
     * 
     * @param int $listId
     * @return array
     * 
     * @todo подгрузка элементов
     */
    protected function loadListById($listId)
    {
        if ( isset($this->_listsCache[$cacheName]) )
        {
            return $this->_listsCache[$cacheName];
        }
        return null;
    }
    
    /**
     * 
     * @param int $listId
     * @return array
     * 
     * @todo функция копирующая элементы одного списка в другой, 
     *       и при этом преобразующая элементы во втором списке в ссылки
     */
    protected function loadListItems($listId)
    {
        return $this->dbConnection->createCommand()->select('*')->
            from('{{easy_list_items}}')->where("easylistid=".$listId)->queryAll();
    } 
    
    /**
     * Create indexes for all fields in the table
     * @param string $table     - table name
     * @param array  $fields    - table fields
     *                            Example:
     *                            array("fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array  $excluded  - not indexed fields
     *                            example: array("fieldname1", "fieldname2", "fieldname3", ...)
     * @param string $idxPrefix - index name prefix (default is "idx_")
     *
     * @return null
     */
    protected function ecCreateIndexes($table, $fields, $excluded = array(), $idxPrefix = "idx_")
    {
        // gather all field names
        $fieldNames = array_keys($fields);
        // exclude not needed fields from index
        // ("id" is already primary key, so we never need to create additional index for it)
        $noIndex       = CMap::mergeArray(array("id"), $excluded);
        $indexedFields = array_diff($fieldNames, $noIndex);
    
        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }
    
    /**
     * Получить список по умолчанию используемый для дополнительных значений настроек модели
     * 
     * @param string $objectType - класс модели
     * @return int
     */
    protected function getDefaultListId($objectType=null)
    {
        $condition = "objecttype='{$objectType}' AND objectid=0 AND 
            name='".Yii::app()->params['defaultListConfig']."'"; 
        
        $config = $this->dbConnection->createCommand()->select('*')->
            from('{{config}}')->where($condition)->queryRow();
        if ( ! $config )
        {// для этой модели такой список не задан
            return ;
        }
        // получаем список из настройки
        $list = $this->dbConnection->createCommand()->select('id')->
            from('{{easy_lists}}')->where("id=".$config['valueid'])->queryRow();
        if ( ! isset($list['id']) )
        {// списка указанного в настройке нет
            return;
        }
        return $list['id'];
    }
}

/*
$config = array(
    'name'         => '',
    'title'        => '',
    'description'  => '',
    'type'         => '',
    'minvalues'    => 0,
    'maxvalues'    => 0,
    'objecttype'   => '',
    'objectid'     => 0,
    'timecreated'  => time(),
    'timemodified' => time(),
    'easylistid'   => 0,
    'valuetype'    => '',
    'valuefield'   => null,
    'valueid'      => 0,
    'parentid'     => 0,
);

$list = array(
    'name'           => '',
    'description'    => '',
    'triggerupdate'  => 'manual',
    'triggercleanup' => 'manual',
    'unique'         => 1,
);


 */