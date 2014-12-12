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
    protected $_configCache       = array();
    /**
     * @var array - системные и корневые настройки, кэшируются здесь чтобы служить
     *              образцом для создания настроек для обычных записей
     *              (для поиска по названию настройки в модели)
     */
    protected $_modelsConfigCache = array();
    /**
     * @var array - списки
     */
    protected $_listsCache       = array();
    
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
            $this->_modelsConfigCache[$config['objecttype']][$config['name']]['0'] = $config;
        }
        return $this->dbConnection->lastInsertID;
    }
    
    /**
     * Создать корневую настройку для модели и прикрепить ее к каждой модели в таблице
     * 
     * @param  array  $configData - изначальные значения записи
     * @param  string $modelTable - таблица для хранения моделей для которых создаются настройки
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
        
        // заполняем созданный список элементами 
        $sortOrder = 1;
        // создаем шаблон для элементов списка
        $itemTemplate = array(
            'easylistid'  => $list['id'],
            'timecreated' => time(),
            'objecttype'  => '__item__',
            'objectid'    => 0,
        );
        foreach ( $items as $value => $data )
        {// заполняем список значениями
            if ( is_array($data) )
            {
                $dataId = 0;
                if ( isset($data['id']) )
                {// убираем id из данных нового элемента если он вдруг случайно туда попал
                    $dataId = $data['id'];
                    unset($data['id']);
                }
                if ( ! isset($data['parentid']) OR ! $data['parentid'] )
                {// сохраняем ссылку на оригинал элемента если нужно
                    $data['parentid'] = $dataId;
                }
                if ( mb_strlen($value) > 1 AND ! intval($value) AND 
                   ( ! isset($data['value']) OR ! $data['value'] ) )
                {
                    $data['value'] = $value;
                }
            }else
            {
                $data = array(
                    'name'     => $data,
                    'value'    => $value,
                );
            }
            $data['sortorder'] = $sortOrder++;
            // создаем элемент списка из шаблона и переданных данных
            $item = CMap::mergeArray($itemTemplate, $data);
            // сохраняем элемент списка / получаем id созданного элемента
            $item['id'] = $this->createListItem($item);
            unset($item);
        }
        return $list['id'];
    }
    
    /**
     * Создать элемент списка
     * 
     * @param  array $itemData
     * @return int
     */
    public function createListItem($itemData)
    {
        // шаблон для элементов этого списка
        $itemTemplate = array(
            'timecreated' => time(),
            'objecttype'  => '__item__',
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
     * Каждый скопированный элемент в новом списке будет помнить о из какой записи был создан
     * 
     * @param int   $fromListId  - id исходного списка (откуда копировать элементы)
     * @param int   $toListId    - id конечного списка (куда копировать элементы)
     * @param array $excludeIds  - исключить из копирования указанные элементы 
     *                             Из списка копируемых элементов будут
     *                             исключены не только элементы с указанными id но и  
     * @param bool  $linkedItems - проверять, являются ли ссылками родительские элементы при 
     *                             создании новых и сохранять ссылки на оригинальный элемент
     * @return bool
     * @throws CException
     */
    public function copyListItems($fromListId, $toListId, $excludeIds=array(), $linkedItems=true)
    {
        if ( ! $fromListId )
        {
            throw new CException('Не указан id исходного списка (откуда копировать элементы)');
        }else
        {// проверяем существование списка с таким id
            $fromList = $this->dbConnection->createCommand()->select('*')->from('{{easy_lists}}')->
                where('id='.$fromListId)->queryRow();
            if ( ! $fromList )
            {
                throw new CException('Исходного списка с таким  id ('.intval($fromListId).') не существует');
            }
        }
        if ( ! $toListId )
        {
            throw new CException('Не указан id конечного списка (куда копировать элементы)');
        }else
        {// проверяем существование списка с таким id
            $toList = $this->dbConnection->createCommand()->select('*')->from('{{easy_lists}}')->
                where('id='.$toListId)->queryRow();
            if ( ! $toList )
            {
                throw new CException('Конечного списка с таким  id ('.intval($toListId).') не существует');
            }
        }
        $items = $this->dbConnection->createCommand()->select('*')->
            from("{{easy_list_items}}")->where('easylistid='.$fromListId)->queryAll();
        $sortOrder = 1;
        
        foreach ( $items as $item )
        {// копируем каждый элемент списка
            // запоминаем id копируемого элемента 
            $itemId   = $item['id'];
            $parentId = $item['parentid'];
            // удаляем неиспользуемые поля
            unset($item['id'], $item['parentid'], $item['easylistid'], $item['sortorder'], 
                $item['timemodified'], $item['timecreated']);
            if ( in_array($itemId, $excludeIds) )
            {// этот элемент не нужно копировать
                continue;
            }
            if ( $keepLinks AND in_array($parentId, $excludeIds) )
            {// этот элемент не нужно копировать
                continue;
            }
            // создаем новый элемент
            $itemTemplate = array(
                'easylistid'   => $toList['id'],
                'objecttype'   => '__item__',
                'timecreated'  => time(),
                'timemodified' => 0,
                'sortorder'    => $sortOrder++,
            );
            // определяем оригинал для копируемого элемента
            if ( $parentId )
            {// исходный элемент сам является ссылкой - копируем его ссылку на оригинал 
                // чтобы не плодить уровни вложенности
                $itemTemplate['parentid'] = $parentId;
            }else
            {// исходный элемент является оригиналом значения - создаем ссылку на него
                $itemTemplate['parentid'] = $itemId;
            }
            // собираем новый элемент из данных исходного элемента и новых данных
            $newItem = CMap::mergeArray($itemTemplate, $item);
            // добавляем элемент в список
            $this->insert('{{easy_list_items}}', $newItem);
            // очищаем временные переменные
            unset($itemTemplate, $newItem);
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
     * 
     * @param string $table     - table name
     * @param array  $columns   - table fields
     *                            Example:
     *                            array("fieldname1" => "fieldtype1", "fieldname2" => "fieldtype2", ... )
     * @param array  $excluded  - not indexed fields
     *                            example: array("fieldname1", "fieldname2", "fieldname3", ...)
     * @param string $idxPrefix - index name prefix (default is "idx_")
     *
     * @return null
     */
    protected function ecCreateIndexes($table, $columns, $excluded=array(), $idxPrefix="idx_")
    {
        // collect all field names
        $fieldNames    = array_keys($columns);
        // exclude not needed fields from index
        // ("id" is already primary key, so we never need to create additional index for it)
        $noIndex       = CMap::mergeArray(array("id"), $excluded);
        $indexedFields = array_diff($fieldNames, $noIndex);
        // create indexes for remaining fields
        foreach ( $indexedFields as $field )
        {
            $this->createIndex($idxPrefix.$field, $table, $field);
        }
    }
    
    /**
     * Получить список по умолчанию используемый для дополнительных значений настроек модели
     * 
     * @param  string $objectType - класс модели для которой нужно получить список по умолчанию
     * @param  bool   $autoCreate - создать список и соответствующую настройку модели 
     *                              автоматически если ее еще нет
     * @return int - id списка
     * @throws CException
     */
    protected function getDefaultListId($objectType, $autoCreate=true)
    {
        $condition = "objecttype='{$objectType}' AND objectid=0 AND name='".Yii::app()->params['defaultListConfig']."'"; 
        // получаем настройку, содержащую список по умолчанию для этой модели
        $config = $this->dbConnection->createCommand()->select('*')->from('{{config}}')->
            where($condition)->queryRow();
        // шаблон для нового списка
        $listTemplate = array(
            'name'           => 'Список по умолчанию для элементов модели '.$objectType.' (создан автоматически)',
            'description'    => 'Это системный список, он используется для того чтобы не создавать '.
            'дополнительные списки при создании промежуточных объектов при совершении миграций. '.
            'Этот список также используется для того чтобы найти битые/испорченные значения '.
            'настроек этой модели, не потеряв при этом вводимые пользователем значения.',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'manual',
            'unique'         => 0,
        );
        // шаблон для настройки для нового списка
        $configTemplate = array(
            'name'            => Yii::app()->params['defaultListConfig'],
            'title'           => 'id списка по умолчанию для элементов модели '.$objectType,
            'description'     => 'Это системная настройка, не изменяйте ее',
            'triggerupdate'   => 'manual',
            'triggercleanup'  => 'manual',
            'objecttype'      => $objectType,
            'objectid'        => 0,
            'timecreated'     => time(),
            'timemodified'    => time(),
            'allowuservalues' => 1,
            'valuetype'       => 'EasyList',
            'valuefield'      => 'id',
            // заполнение не обязательно
            'minvalues'       => 0,
            // содержит одиночное значение (id)
            'maxvalues'       => 1,
        );
        if ( ! $config )
        {// для этой модели нет нужной настройки
            if ( ! $autoCreate )
            {
                throw new CException('Не найдена настройка с id списка по умолчанию для элементов модели "'.$objectType.'"');
            }
            $list   = CMap::copyFrom($listTemplate);
            $config = CMap::mergeArray($config, $configTemplate);
            // создадим недостающий список
            $this->insert('{{easy_lists}}', $list);
            $list['id'] = $this->dbConnection->lastInsertID;
            // создадим настройку модели для хранения id списка
            $config = array(
                'valueid' => $list['id'],
            );
            $config['id'] = $this->createConfig($config);
        }else
        {// получаем список из настройки
            $list = $this->dbConnection->createCommand()->select('id')->from('{{easy_lists}}')->
                where("id=".$config['valueid'])->queryRow();
            if ( ! isset($list['id'])  )
            {// список, указанный в настройке модели не существует или еще не создан
                if ( ! $autoCreate )
                {
                    throw new CException('Не найден список по умолчанию для элементов модели "'.$objectType.'"');
                }
                $list = CMap::copyFrom($listTemplate);
                // создадим недостающий список
                $this->insert('{{easy_lists}}', $list);
                $list['id'] = $this->dbConnection->lastInsertID;
                // обновим настройку
                $newData = array(
                    'valuetype'  => 'EasyList',
                    'valuefield' => 'id',
                    'valueid'    => $list['id'],
                );
                $this->update('{{config}}', $newData, 'id='.$config['id']);
            }
        }
        // возвращаем id найденного (или созданного) списка
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
    'valuetype'    => 'EasyListItem',
    'valuefield'   => null, // id
    'valueid'      => 0,
    'parentid'     => 0,
    'allowuservalues' => 0,
);

$list = array(
    'name'           => '',
    'description'    => '',
    'triggerupdate'  => 'manual',
    'triggercleanup' => 'manual',
    'unique'         => 1,
);


 */