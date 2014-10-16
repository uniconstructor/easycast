<?php

/**
 * Дополнительные функции для моделей, работающих с настройками
 * Это поведение позволит добавлять произвольное количество настроек к любой AR-модели
 * Каждая настройка может иметь собственный список стандартных вариантов значений для выбора
 * В качестве списка стандартных значений моджет быть использован любой список (EasyList) в системе
 * 
 * Поведение содержит стандартные именованные группы условий поиска для получения настроек, привязанных
 * к модели а также функции для поиска моделей по значениям настроек
 * Каждая модель обладает списком настроек, который является общим для всех экземпляров класса,
 * но при этом каждый экземпляр класса может иметь собственные значения для каждой настройке
 *
 * К стандартному списку настроек модели относятся только настройки для которых: 
 * `objecttype` = 'класс_модели' и objectid = 0
 * 
 * Поведение использует настройки родительских моделей как шаблоны (значения по умочанию) для
 * новых настроек модели, к которой прикрепляется поведение. Иерархия моделей может быть любой,
 * но модели нижнего уровня должны содержать в себе связь (relation) с родительскими моделями. 
 * Тип связи должен быть BELONGS_TO или HAS_ONE
 * Если иерархия для настроек не задана - то в настройках новых моделей в качестве значений по 
 * умолчанию будут использоваться корневые настройки модели (это настройки с objectid=0) 
 * 
 * Этот класс также отвечает за целостность таблицы настроек:
 * - создает все нужные настройки при создании новой модели
 * - удаляет связанные настройки при удалении модели
 * При этом об исправлении ссылок на удаленные настройки позаботится сама модель Config   
 * во время своего удаления (используя beforeDelete())
 * 
 * Поведение содержит стандартные методы для получения и сохранения значения настройки связанной
 * модели, поэтому все действия с настройками модели должны происходить через методы этого поведения
 * Оно также ведет историю изменений настроек, отслеживая какие настройки меняются пользователями и как
 * 
 * При соеднинении с моделью поведение дополняет список ее сязей (relations): это позволит
 * стандартным образом искать любые модели по значениям настроек, используя именованные группы
 * условий поиска (scopes)
 * Если модель уже имеет связи с такими именами, то будет создано исключение
 * 
 * Виджет редактирования настроек также опирается на это поведение для получения полного списка
 * всех настроек для отображения формы редактирования
 * 
 * Обратите внимание: Задавать полный список всех настроек модели при подключении поведения не нужно,
 * этот список извлекается из таблицы {{config}} по objecttype/objectid (для существующей модели)
 * 
 * Для определения того, какой изначальный список настроек нужен для новой модели мы извлекаем
 * все настройки у которых:
 * - objecttype содержит имя класса owner-модели 
 * - objectid=0
 * Такие настройки называются корневыми
 * Для одного класса модели не может существовать двух настроек с одинаковым name
 * Нельзя добавить новую настройку к модели, не создав сначала корневую настроку с таким же именем
 * 
 * Значения настроек редактируются отдельно от значений модели, как правило стандартным виджетом:
 * поэтому синхронизация редактирования модели и редактирования настроек не требуется
 * 
 * @todo проверка: существует ли требуемая настройки для указанной модели
 * @todo проверка: отличается ли значение настройки от стандартного/корневого
 * @todo проверка: существует ли корневая настройка для такого класса модели с таким именем 
 *       (перед сохранением новой настройки)
 * @todo создать переменную для кеширования загруженных настроек
 * @todo добавить использование defaultParentRelation
 * @todo добавить использование customConfigParents
 * @todo добавить phpdoc-заготовку для codeAssist в owner-моделях
 */
class ConfigurableRecordBehavior extends CActiveRecordBehavior
{
    /**
     * @var array - связи модели (relations), содержащие в себе объекты, являющиеся шаблонами 
     *              для настроек текущей модели ($this->owner) 
     *              Для разных настроек могут быть заданы разные связи (relations). 
     *              Настройки из этих связанных объектов будут использованы как шаблоны 
     *              ($config->parentConfig) при создании настроек для текущей модели ($this->owner) 
     *              
     *              Пример $this->parentRelations для модели мероприятия (ProjectEvent): 
     *              array(
     *                  // максимальное количество изображений берется из модели галереи
     *                  'galleryLimit' => 'gallery',
     *                  // значение подcтавляемое в поле "тип события" при создании новой записи
     *                  // берется из настроек проекта
     *                  // здесь ключ массива - это название связи, а значение - название настройки
     *                  // в связанном объекте
     *                  'defaultType'  => array('project' => 'defaultEventType'),
     *              )
     *              
     *              Связь должна указывать на модель использующую настройки: произойдет поиск прикрепленной
     *              к модели настройки с именем, указанным в ключе массива, и будет получено ее значение
     */
    public $customConfigParents = array();
    /**
     * @var string - значение для имени связи (relation) со "стандартной родительской моделью"
     *               Это связь должна существовать в owner-модели иметь тип HAS_ONE/BELONGS_TO 
     *               Если связь указывает на существующую модель, то к ней также должны быть 
     *               прикреплены настройки
     *               Допустимо имя связи с точками, например: "event.project"
     *               Каждый раз когда создается новая owner-модель к ней после первого сохранения
     *               должен быть прикреплен изначальный набор настроек
     *               Значения для этого набора настроек настроек как раз и будут будут взяты из
     *               этой связанной модели
     *               
     *               Если связь не указывает на существующую модель или в связанной модели нет настройки
     *               с таким же именем - то значение для новой настройки owner-модели будет взято из
     *               "корневой" настройки owner-модели 
     *               (корневые настройки имеют objectid=0 при фиксированном objecttype)
     */
    public $defaultParentRelation;
    /**
     * @var string - класс модели к которому добавляются настройки: в большинстве случаев не
     *               требует указания значения (определяется автоматически)
     */
    public $defaultOwnerClass;
    /**
     * @var array - массив с дополнительными связями (relations) для owner-модели
     *              Часто используемые настройки модели можно выносить сюда, чтобы 
     *              к ним было удобнее обращаться
     */
    public $customRelations = array();
    /**
     * @var string - название связи которая хранит все настройки модели
     */
    public $configRelationName = 'configParams';
    
    /**
     * @return array
     * @see CActiveRecord::relations()
     *
     * @todo создавать исключение если связи с такими именами в модели уже есть
     */
    public function relations()
    {
        // добавляем к стандартной связи все указанные в параметрах поведения при подключении модели
        $this->customRelations = CMap::mergeArray($this->getDefaultConfigRelations(), $this->customRelations);
        // получаем существующие связи модели
        $modelRelations = $this->owner->relations();
        // возвращаем список в котором совмещены связи модели ниши связи через составно внешний ключ
        return CMap::mergeArray($modelRelations, $this->customRelations);
    }
    
    /**
     * Создает полный список настроек при создании модели
     * @see CActiveRecordBehavior::beforeSave()
     * 
     * @todo детальнее проработать копирование родительской настройки: брать данные не только из корневой записи
     */
    public function afterSave($event)
    {
        parent::afterSave($event);
        
        if ( $this->owner->isNewRecord )
        {// после создания каждой записи создаем для нее стандартный набор настроек
            $configParams = $this->getRootConfigParams();
            foreach ( $configParams as $configParam )
            {// из корневых настроек делаем настройки для модели
                // копируем все основные данные из шаблона (родительской настройки)
                $attributes = $configParam->attributes;
                unset($attributes['timecreated']);
                unset($attributes['timemodified']);
                unset($attributes['objectid']);
                unset($attributes['parentid']);
                // привязываем новую настройку к созданной модели
                $config = new Config();
                $config->attributes  = $attributes;
                $config->objectid    = $this->owner->id;
                $config->parentid    = $configParam->id;
                if ( ! $config->save() )
                {// не удалось сохранить или прикрепить новую настройку
                    // @todo удалить все прикрепленные до этого настройки если они есть или использовать
                    //       транзакцию перед созданием настроек модели
                    throw new CException('Не удалось прикрепить настройку к новой модели');
                }
            }
        }
    }
    
    /**
     * Удаляет все связанные настройки после удаления модели
     * @see CActiveRecordBehavior::afterDelete()
     */
    public function afterDelete($event)
    {
        parent::afterDelete($event);
        
        foreach ( $this->owner->$configRelationName as $configParam )
        {// после удаления модели удяляем все связанные с ней настройки
            if ( ! $configParam->delete() )
            {// @todo использовать транзакцию
                throw new CException('Не удалось удалить настройку при удалении модели');
            }
        }
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка 
     * c указанным служебным названием (или хотя бы одним названием из списка если передан массив)
     *
     * @param  string|array $name - служебное название настройки (или список названий)
     * @return CActiveRecord
     */
    public function withConfigName($name, $operation='AND')
    {
        if ( ! $name )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes' => array(
                    'withName' => array($name),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка 
     * c указанным parentid (или хотя бы одним parentid из списка если передан массив)
     *
     * @param  string|array $parentId - служебное название настройки (или список названий)
     * @return CActiveRecord
     */
    public function withConfigParentId($parentId, $operation='AND')
    {
        if ( ! $parentId )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes' => array(
                    'withParentId' => array($parentId),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка 
     * c указанным id (или хотя бы одним id из списка если передан массив)
     *
     * @param  int|array $configId - id настройки в таблице config (или список таких id)
     * @return CActiveRecord
     */
    public function withConfigId($configId, $operation='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes' => array(
                    'withId' => array($configId),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * (alias) Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка
     * в которой выбран переданный вариант значения 
     * (или хотя бы одним значением из списка если передан массив)
     * 
     *
     * @param  int|array $optionId - id варианта значения настройки (EasyListItem)
     * @return CActiveRecord
     */
    public function withConfigOptionId($optionId, $operation='AND')
    {
        return $this->withAnyConfigOptionId($optionId, $operation);
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка
     * в которой выбран переданный вариант значения 
     * (или хотя бы одним значением из списка если передан массив)
     *
     * @param  int|array $options - id варианта значения настройки (EasyListItem)
     * @return CActiveRecord
     */
    public function withAnyConfigOptionId($options, $operation='AND')
    {
        if ( ! $options )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes' => array(
                    'withAnySelectedOption' => array($options),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть настройка в которой выбран
     * каждый переданный вариант значения из списка
     *
     * @param  array $options - массив из id вариантов значения настройки (EasyListItem)
     * @return CActiveRecord
     */
    public function withEveryConfigOptionId($options, $operation='AND')
    {
        if ( ! $options )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes' => array(
                    'withEverySelectedOption' => array($options),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * (alias) Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка
     * c указанным значением (или хотя бы одним значением из списка если передан массив)
     *
     * @param  string|array $value - значение настройки
     * @return CActiveRecord
     */
    public function withConfigValue($value, $operation='AND')
    {
        return $this->withAnyConfigValue($value, $operation);
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка
     * c указанным значением (или хотя бы одним значением из списка если передан массив)
     *
     * @param  string|array $values - значение настройки
     * @return CActiveRecord
     */
    public function withAnyConfigValue($values, $operation='AND')
    {
        if ( ! $values)
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes' => array(
                    'withAnySelectedValue' => array($values),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка
     * в которой выбрано каждое из указаных значений
     *
     * @param  string|array $values - список значений настройки
     * @return CActiveRecord
     */
    public function withEveryConfigValue($values, $operation='AND')
    {
        if ( ! $values)
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes' => array(
                    'withEverySelectedValue' => array($values),
                ),
            ),
        );
        $criteria->together = true;
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Получить полный список всех настроек owner-модели
     * 
     * @param string $model - класс модели для которого нужно получить список настроек
     *                        По умолчанию будет использован класс owner-модели
     * @param int    $id    - id модели для которой нужно получить список настроек
     * @return Config[] - массив, содержащий все доступные для этой модели настройки
     *                    или пустой массив, если настройки для этой модели не предусмотрены
     *                    Если модель еще не сохранена - то возвращает список корневых настроек модели
     */
    public function getModelConfigParams($model=null, $id=0)
    {
        if ( ! $model )
        {
            $model = $this->getOwnerClass();
        }
        if ( ! $id )
        {
            $id = $this->owner->id;
        }
        return Config::model()->forObject($model, $id)->findAll();
    }
    
    /**
     * Получить полный список родительских настроек owner-модели: получить все настройки модели, 
     * но значение каждой настройки будет взято из родительского объекта (Config->configParent)
     * (ближайшего в цепочке наследования если настройка имеет несколько родительских)
     * 
     * @param string $model - класс модели для которого нужно получить список настроек
     *                        По умолчанию будет использован класс owner-модели
     * @param int    $id    - id модели для которой нужно получить список настроек
     * @return Config[] - массив, содержащий все родительские настройки для этой модели
     *                    или пустой массив, если настройки для этой модели не предусмотрены
     *                    Если модель еще не сохранена - то возвращает список корневых настроек модели
     * 
     * @todo
     */
    /*public function getParentConfigParams($model=null, $id=0)
    {
        if ( ! $model )
        {
            $model = $this->getOwnerClass();
        }
        if ( ! $id )
        {
            $id = $this->owner->id;
        }
        
    }*/
    
    /**
     * Получить полный список всех корневых настроек owner-модели
     * 
     * @param string $model - класс модели для которого нужно получить список настроек
     *                        По умолчанию будет использован класс owner-модели
     * @return Config[] - массив, содержащий все доступные корневые настройки для выбранной модели
     *                    или пустой массив, если настройки для этой модели не предусмотрены
     */
    public function getRootConfigParams($model=null)
    {
        if ( ! $model )
        {
            $model = $this->getOwnerClass();
        }
        return Config::model()->forObject($model, 0)->findAll();
    }
    
    /**
     * Узнать существует ли (и подключена ли) настройка с таким именем для текущей owner-модели
     *
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @param  string $model - класс модели (по умолчанию используется класс owner-модели)
     * @return bool
     */
    public function hasConfig($name, $model=null)
    {
        return Config::model()->forModel($this)->withName($name)->exists();
    }
    
    /**
     * Получить значение настройки по ее названию или все настройки модели если название не указано
     * Этот метод также работает как геттер, позволяя обращаться к любой настройке любой модели
     * удобным способом: $model->config['configName'];
     * 
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @return string|array - значение настройки:
     *                        * строка или число (для одиночных настроек) 
     *                        * массив значений настройки (для настроек с множественным выбором)
     *                        * массив всех настроек модели со всеми значениями 
     *                          (если название настройки не указано)
     *                        Если возвращается массив всех настроек модели - то ключами в нем
     *                        всегда являются имена настроек а значениями - строки (для одиночных настроек)
     *                        или массивы строк (для настроек с множественным выбором)
     * 
     * @todo кеширование
     */
    public function getConfig($name=null)
    {
        if ( ! $name )
        {// название не указано - выводим все настройки
            $params = $this->getModelConfigParams();
            return $this->configParamsToArray($params);
        }
        // выводим значение для одной настройки
        return $this->getConfigValue($name);
    }
    
    /**
     * Получить изначальное значение настройки по ее названию
     *
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @return string|array - значение настройки:
     *                        * строка или число (для одиночных настроек)
     *                        * массив значений настройки (для настроек с множественным выбором)
     *
     * @todo кеширование
     */
    public function getConfigDefaultValue($name=null)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        return $config->getDefaultValue();
    }
    
    /**
     * Получить модель привязанной к owner настройки с указанным именем
     * 
     * @param  string|array $name - служебное имя настройки (поле name в модели Config)
     * @return Config
     * 
     * @todo проверить что при указании $name находится не более 1 записи
     */
    public function getConfigObject($name)
    {
        return Config::model()->forModel($this)->withName($name)->find();
    }
    
    /**
     * Получить значение настройки по ее имени
     * 
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @return string|array - значение настройки:
     *                        * строка или число (для одиночных настроек) 
     *                        * массив значений настройки (для настроек с множественным выбором)
     * @throws CException
     */
    public function getConfigValue($name)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        // настройку нашли - возвращаем значение
        return $config->value;
    }
    
    /**
     * Получить отображаемое в интерфейсе название настройки по ее служебному имени
     *
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @return string       - название настройки
     * @throws CException
     */
    public function getConfigTitle($name)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        // настройку нашли - возвращаем значение
        return $config->title;
    }
    
    /**
     * Получить список выбранных значений для настройки с указанным именем
     *
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @return array
     *
     * @todo кеширование
     */
    public function getConfigSelectedOptions($name)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        return $config->getSelectedOptions();
    }
    
    /**
     * Получить список значений по умолчанию для настройки с указанным именем
     *
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @return array
     *
     * @todo кеширование
     */
    public function getConfigDefaultOptions($name)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        return $config->getDefaultOptions();
    }
    
    /**
     * Получить отображаемое в интерфейсе название варианта значения, который выбран
     * в данный момент в указанной настройке по id выбранного варианта
     * 
     * @param  string $name     - служебное имя настройки (поле name в модели Config)
     * @param  int    $optionId - id выбранного значения настройки (как правило EasyListItem)
     *                            Также может быть id модели из любой другой таблицы - в зависимости
     *                            от значения valuetype в выбранной настройке
     *                            (используется чтобы указать какой именно из 
     *                            выбранных вариантов переводить)
     * @return string|array
     */
    public function getSelectedOptionTitle($name, $optionId=null)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        if ( ! $optionId AND $config->isMultiple() )
        {// непонятно какой из вариантов переводить
            throw new CException('Для настроек с множественным выбором $optionId обязателен');
        }
        if ( $config->valuetype === 'EasyListItem' OR ($config->valuetype === 'EasyList' AND $config->isMultiple()) )
        {// выбранное значение настройки является стандартным элементом EasyListItem
            return $this->getConfigOptionTitle($optionId);
        }
        if ( $config->isSingle() )
        {// для всех остальных вариантов: проверяем стандартные поля моделей
            if ( ! $value = $config->selectedValue )
            {
                return '((нет))';
            }elseif ( isset($value->caption) )
            {
                return $value->caption;
            }elseif ( isset($value->title) )
            {
                return $value->title;
            }elseif ( isset($value->name) )
            {
                return $value->name;
            }
        }else
        {// множественный выбор но модели не EasyListItem: так не должно быть
            throw new CException('Для настройки id='.$config->id.' указан неправильный valuetype
                при использовании множественного выбора 
                (должен быть EasyList, указан "'.$config->valuetype.'")');
        }
        return '((нет))';
    }
    
    /**
     * Получить отображаемое в интерфейсе название варианта значения настройки по id варианта из списка
     * 
     * @param  int    $optionId - id выбранного значения настройки (EasyListItem)
     * @return string
     */
    public function getConfigOptionTitle($optionId)
    {
        // для настроек с несколькими значениями: значения таких настроек всегда хранятся в моделях EasyListItem
        if( $item = EasyListItem::model()->findByPk($optionId) )
        {
            return '--(not_found)--';
        }
        return $item->title;
    }
    
    /**
     * Преобразовать список моделей настроек в ассоциативный массив содержащий только значения настроек
     * 
     * @param Config[] $params - массив моделей настроек из базы
     * @return array - массив значений настроек:
     *                 array(
     *                     'singleConfigName1'=> 'my_config_value',
     *                     'multionfigName2'  => array(
     *                         '42' => 'item42_value', // EasyListItem.id => EasyListItem.value
     *                         '43' => 'item43_value', // EasyListItem.id => EasyListItem.value
     *                         ...
     *                     ),
     *                     ...
     *                 )
     */
    protected function configParamsToArray($params)
    {
        $result = array();
        // извлекаем значение из каждой настройки при пор
        foreach ( $params as $param )
        {/* @var $item Config */
            $result[$param->name] = $param->value;
        }
        return $result;
    }
    
    /**
     * Получить класс AR-модели к которой привязаны настройки
     * 
     * @return string
     */
    protected function getOwnerClass()
    {
        if ( ! $this->defaultOwnerClass )
        {
            $this->defaultOwnerClass = get_class($this->owner);
        }
        return $this->defaultOwnerClass;
    }
    
    /**
     * Создать связь с целевым объектом (к которому прикрепляется модель)
     * опираясь на значения по умолчанию
     *
     * @return array
     */
    protected function getDefaultConfigRelations()
    {
        // задаем имя и параметры связи owner-модели cо списком настроек
        return array(
            $this->configRelationName => array(
                self::HAS_MANY,
                'Config',
                $this->objectid,
                'scopes' => array(
                    'withObjectType' => array($this->getOwnerClass()),
                ),
            ),
        );
    }
    
    /**
     * Именованая группа условий: все настройки, содержащие указаный вариант стандартного значения
     *
     * @param EasyListItem|int|array $option
     * @return Config
     */
    /*public function withSelectedDefaultOption($option)
     {
     
    }*/
    
    /**
     * Именованая группа условий: все настройки, содержащие указаный вариант пользовательского значения
     *
     * @param EasyListItem|int|array $option
     * @return Config
     */
    /*public function withSelectedUserOption($option)
     {
     
    }*/
    
    /**
     * Именованая группа условий: все настройки, содержащие указаный вариант пользовательского значения
     *
     * @param EasyListItem|int|array $option
     * @return Config
     */
    /*public function withUserConfigOption($option)
     {
     
    }*/
}