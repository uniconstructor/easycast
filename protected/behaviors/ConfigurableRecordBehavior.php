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
 * @todo кеширование загруженных настроек
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
    public $customRelations    = array();
    /**
     * @var string - название связи которая хранит все настройки модели
     */
    public $configRelationName = 'configParams';
    /**
     * @var array - список настроек которые должны быть созданы автоматически в момент создания модели
     *              Содержит только имена настроек ($config->name)
     *              В этом списке могут быть только настройки из списка $this->getRootConfigParams()
     */
    public $autoCreate         = array();
    
    /**
     * @see CActiveRecord::relations()
     * 
     * @return array
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
     * @param  CModelEvent $event
     * @return void
     * 
     * @todo детальнее проработать копирование родительской настройки: 
     *       брать данные не только из корневой записи
     */
    public function afterSave($event)
    {
        if ( $this->owner->isNewRecord )
        {// если для этой модели есть настройки, которые надо создать вместе с ней автоматически
            // то создадим для нее стандартный набор настроек
            foreach ( $this->getAutoCreatedParams() as $configParam )
            {// из корневых настроек делаем настройки для модели
                // копируем все основные данные из шаблона (родительской настройки)
                $attributes = $configParam->attributes;
                // удаляем те параметры которые не нужны в новой настройке
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
                    $msg = 'Не удалось прикрепить настройку к новой модели (modelClass='.
                        $this->getOwnerClass().') (configName='.$configParam->name.')';
                    Yii::log($msg, CLogger::LEVEL_ERROR, 'application.config');
                    throw new CException($msg);
                }
            }
        }
        parent::afterSave($event);
    }
    
    /**
     * Удаляет все связанные настройки после удаления модели
     * @see CActiveRecordBehavior::afterDelete()
     * 
     * @param  CModelEvent $event
     * @return void
     */
    public function beforeDelete($event)
    {
        if ( ! $this->owner->hasRelated($this->configRelationName) )
        {// удаляемая модель не содержит привязанных настроек
            return;
        }
        foreach ( $this->owner->getRelated($this->configRelationName) as $configParam )
        {// после удаления модели удяляем все связанные с ней настройки
            if ( ! $configParam->delete() )
            {// ошибка при удалении записи
                $event->isValid = false;
                throw new CException('Не удалось удалить настройку при удалении модели');
            }
        }
        parent::beforeDelete($event);
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка 
     * c указанным служебным названием (или хотя бы одним названием из списка если передан массив)
     *
     * @param  string|array $name  - служебное название настройки (или список названий)
     * @param  string   $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
     * @param  string|array $parentId  - служебное название настройки (или список названий)
     * @param  string       $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
     * @param  int|array $optionId - id варианта значения настройки (EasyListItem)
     * @param  string   $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
     * @param  int|array $options   - id варианта значения настройки (EasyListItem)
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
                'scopes'   => array(
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
     * @param  array  $options   - массив из id вариантов значения настройки (EasyListItem)
     * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
     * в которой выбрано каждое из указаных значений
     *
     * @param  string|array $optionId - массив из id вариантов значения настройки (EasyListItem)
     * @param  string    $operation   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptConfigOptionId($optionId, $operation='AND')
    {
        return $this->exceptAnyConfigOptionId($optionId, $operation='AND');
    }
    
    /**
     * Условие поиска по настройкам: все записи у которых есть хотя бы одна настройка
     * в которой выбрано каждое из указаных значений
     *
     * @param  string|array $option - массив из id вариантов значения настройки (EasyListItem)
     * @param  string   $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptAnyConfigOptionId($optionId, $operation='AND')
    {
        if ( ! $optionId )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->with = array(
            $this->configRelationName => array(
                'select'   => false,
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'exceptSelectedOption' => array($optionId),
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
     * @param  string   $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
     * @param  string   $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
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
     * @param  string   $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withEveryConfigValue($values, $operation='AND')
    {
        if ( ! $values )
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
     * Полныи списком считается список всех корневых настроек модели ($this->getRootConfigParams())
     * Если к модели не привязана нужная настройка - то вместо нее используется 
     * корневая с таким же именем, и ее значение считается значением настройки модели
     * 
     * @param  bool $checkRoot - включать ли в список настроек модели корневые настройки
     *                           в качестве настроек по умолчанию?
     * @return Config[] - массив, содержащий все доступные для этой модели настройки
     *                    или пустой массив, если настройки для этой модели не предусмотрены
     *                    Если модель еще не сохранена - то возвращает список корневых настроек модели
     */
    public function getModelConfigParams($checkRoot=true)
    {
        $model = $this->getOwnerClass();
        $id    = $this->owner->id;
        
        $notAttachedParams = array();
        if ( $checkRoot )
        {// для настроек которые еще ни разу не редактировались пользователем используются
            // одноименные настройки по умолчанию 
            // (они не привязаны к конкретной модели и используются в качестве значений по умолчанию)
            $notAttachedParams = $this->getNotAttachedConfigParams();
        }
        // получаем все настройки, привязанные к этой модели
        $attachedParams = $this->getAttachedConfigParams();
        
        return CMap::mergeArray($attachedParams, $notAttachedParams);
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
     * @return Config[] - массив, содержащий все доступные корневые настройки для выбранной модели
     *                    или пустой массив, если настройки для этой модели не предусмотрены
     */
    public function getRootConfigParams()
    {
        $model = $this->getOwnerClass();
        return Config::model()->forObject($model, 0)->setResultIndex('name')->findAll();
    }
    
    /**
     * Получить список настроек, которые должны быть автоматически созданы вместе с owner-моделью
     * 
     * @return Config[] - массив корневых настроек, из которых будут создаваться настройки модели
     */
    public function getAutoCreatedParams()
    {
        if ( ! $this->autoCreate )
        {// для этой модели нет настроек которые должны быть созданы автоматически
            return array();
        }
        $model = $this->getOwnerClass();
        return Config::model()->withName($this->autoCreate)->forObject($model, 0)->findAll();
    }
    
    /**
     * Получить только привязанные к этой модели настройки
     * 
     * @return Config[]
     */
    public function getAttachedConfigParams()
    {
        $model = $this->getOwnerClass();
        $id    = $this->owner->id;
        return Config::model()->forObject($model, $id)->setResultIndex('name')->findAll();
    }
    
    /**
     * Получить настройки модели, которые есть в списке корневых,
     * но еще не привязаны к этой модели 
     * (т. к. настройка создается и привязывается к модели только если ее значение было
     * изменено пользователем) 
     * 
     * @return Config[]
     */
    public function getNotAttachedConfigParams()
    {
        if ( ! $rootSettings = $this->getRootConfigParams() )
        {
            return array();
        }
        if ( ! $attachedSettings = $this->getAttachedConfigParams() )
        {
            return $rootSettings;
        }
        // вычисляем какие настройки из полного набора еще не имеют собственного экземпляра
        // привязанного к этой модели (другими словами проверяем каких значений из $rootSettings
        // не хватает в $attachedSettings)
        return array_diff_key($rootSettings, $attachedSettings);
    }
    
    /**
     * Узнать существует ли настройка с таким именем для текущей owner-модели
     * Если название настройки не указано - то функция проверит 
     * есть ли хотя бы одна настройка у этой модели
     *
     * @param  string $name      - служебное название настройки (поле name в модели Config)
     * @param  bool   $checkRoot - искать ли корневую настройку с таким же названием
     *                             если для owner-модели нет настройки с таким именем?
     *                             (По умолчанию true)
     *                             При значении false функция будет искать только настройки
     *                             привязанные к owner-модели
     * @return bool
     */
    public function hasConfig($name=null, $checkRoot=true)
    {
        if ( $name )
        {// проверяем есть ли у модели настройка с указанным именем
            if ( $this->getConfigObject($name, $checkRoot) )
            {
                return true;
            }
        }else
        {// проверяем есть ли модели хотя бы одна настройка
            if ( $this->getModelConfigParams($checkRoot) )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Получить значение настройки по ее названию или все настройки модели если название не указано
     * Этот метод также работает как геттер, позволяя обращаться к любой настройке любой модели
     * удобным способом: $model->config['configName'];
     * 
     * @param  string $name      - служебное имя настройки (поле name в модели Config)
     * @param  bool   $checkRoot - искать ли корневую настройку с таким же названием
     *                             если для owner-модели нет настройки с таким именем?
     * @return string|array - значение настройки:
     *                        * строка или число (для одиночных настроек) 
     *                        * массив значений настройки (для настроек с множественным выбором)
     *                        * массив всех настроек модели со всеми значениями 
     *                          (если название настройки не указано)
     *                        Если возвращается массив всех настроек модели - то ключами в нем
     *                        всегда являются имена настроек а значениями - строки 
     *                        (для одиночных настроек) или массивы строк 
     *                        (для настроек с множественным выбором)
     * 
     * @todo кеширование
     */
    public function getConfig($name=null, $checkRoot=true)
    {
        if ( ! $name )
        {// название не указано - выводим все настройки
            $params = $this->getModelConfigParams($checkRoot);
            return $this->configParamsToArray($params);
        }
        // выводим значение для одной настройки
        return $this->getConfigValue($name, $checkRoot);
    }
    
    /**
     * Получить настройку для редактирования ее значения
     * Если к текущей owner-модели настройка с таким именем пока еще не привязана - то из данных 
     * корневой настройки для owner-модели будет создана и привязана новая настройка (Config)
     * Если для owner-модели нет корневой настройки с нужным названием то будет
     * выброшено исключение
     * 
     * @param  string $name - служебное название настройки (поле name в модели Config)
     * @return Config|bool  - привязанная к owner-модели настройка
     * @throws CException
     */
    public function getEditableConfig($name)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        return $config->getEditableConfig(get_class($this->owner), $this->owner->id);
    }
    
    /**
     * Получить модель привязанной к owner настройки с указанным именем
     *
     * @param  string|array $name - служебное имя настройки (поле name в модели Config)
     * @param  bool    $checkRoot - искать ли корневую настройку с таким же названием
     *                              если для owner-модели нет настройки с таким именем?
     * @return Config
     *
     * @todo проверить что при указании $name находится не более 1 записи
     */
    public function getConfigObject($name, $checkRoot=true)
    {
        $config = Config::model()->forModel($this->owner)->withName($name)->find();
        if ( ! $config AND $checkRoot )
        {// ищем родительскую настройку с таким же названием если ее нет для owner-модели
            $config = $this->getRootConfig($name);
        }
        return $config;
    }
    
    /**
     * Получить значение настройки по ее имени
     *
     * @param  string $name    - служебное имя настройки (поле name в модели Config)
     * @param  bool $checkRoot - искать ли корневую настройку с таким же названием
     *                           если для owner-модели нет настройки с таким именем?
     * @return string|array - значение настройки:
     *                        * строка или число (для одиночных настроек)
     *                        * массив значений настройки (для настроек с множественным выбором)
     * @throws CException
     */
    public function getConfigValue($name, $checkRoot=true)
    {
        /* @var $config Config */
        if ( ! $config = $this->getConfigObject($name, $checkRoot) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        // настройку нашли - возвращаем значение
        return $config->value;
    }
    
    /**
     * Получить объект содержащий значение настройки по имени настройки
     *
     * @param  string $name - служебное имя настройки (поле name в модели Config)
     * @param  bool $checkRoot - искать ли корневую настройку с таким же названием
     *                           если для owner-модели нет настройки с таким именем?
     * @return string|array - значение настройки:
     *                        * строка или число (для одиночных настроек)
     *                        * массив значений настройки (для настроек с множественным выбором)
     * @throws CException
     */
    public function getConfigValueObject($name, $checkRoot=true)
    {
        /* @var $config Config */
        if ( ! $config = $this->getConfigObject($name, $checkRoot) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка с именем "'.$name.'"');
        }
        // настройку нашли - возвращаем значение
        return $config->getValueObject();
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
    public function getConfigDefaultValue($name)
    {
        if ( ! $config = $this->getRootConfig($name) )
        {// настройки с таким названием нет в списке настроек этой модели -
            throw new CException('Для модели "'.get_class($this->owner).
                '" не найдена настройка корневая настройка с именем "'.$name.'"');
        }
        return $config->value;
    }
    
    /**
     * Получить модель настройки привязанной ко всем объектам класса owner-модели
     * (возвращает родительскую настройку модели с указанным мсенем)
     *
     * @param  string|array $name - служебное имя настройки (поле name в модели Config)
     * @return Config
     *
     * @todo проверить что при указании $name находится не более 1 записи
     */
    public function getRootConfig($name)
    {
        $objectType = $this->getOwnerClass();
        return Config::model()->forObject($objectType, 0)->withName($name)->find();
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
    public function getSelectedConfigOptions($name)
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
    public function getDefaultConfigOptions($name)
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
     * @throws CException
     */
    public function getSelectedConfigOptionTitle($name, $optionId=null)
    {
        if ( ! $config = $this->getConfigObject($name) )
        {// настройки с таким названием нет в списке настроек этой модели
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
        if( ! $item = EasyListItem::model()->findByPk($optionId) )
        {
            return '--(none)--';
        }
        return $item->title;
    }
    
    /**
     * Изменить один элемент значения настройки с множественным выбором.
     * Добавдяет в список выбранных значений элемент если его там не было
     * или удаляет его из этого списка если он там был
     * 
     * @param  string           $name   - служебное название настройки
     * @param  int|EasyListItem $option - изменяемый элемент значения настройки: берется из списка 
     *                                    стандартных или пользовательских значений
     * @return bool
     * 
     * @todo проверка isMultiple()
     * @todo продумать вариант добавления нового значения в пользовательский список
     * @todo доработать для настроек с одним значением
     * @todo создавать пользовательский список если настройка должна содержать список значений 
     */
    public function toggleConfigOption($name, $option)
    {
        if ( ! $option )
        {
            throw new CException('Не передан изменяемый элемент списка');
        }
        // получаем привязанную настройку модели, если ее нет - то создаем и привязываем 
        $config = $this->getEditableConfig($name);
        // получаем переключаемый элемент списка
        if ( $option instanceof EasyListItem )
        {// передан элемент полностью
            $item = $option;
        }elseif ( is_numeric($option) )
        {// передан id элемента списка
            $listIds = array();
            if ( $config->easylistid )
            {// ищем элемент в списке стандартных значений настройки
                $listIds[] = $config->easylistid;
            }
            if ( $config->userlistid )
            {// ищем элемент в списке дополнительных (введенных пользователем) значений настройки
                $listIds[] = $config->userlistid;
            }
            if ( $config->isMultiple() AND $config->valuetype === 'EasyList' AND $config->valueid )
            {// ищем элемент в списке выбранных значений настройки
                // @todo применять только при удалении битых значений настроек:
                // они есть в списке выбранных, но отсутствуют в пользовательском и стандартном
                $listIds[] = $config->valueid;
            }
            // получаем элемент списка целиком
            $item = EasyListItem::model()->withListId($listIds)->withItemId($option)->
                find(array('limit' => 1));
            if ( ! $item )
            {
                throw new CException('В модели "'.get_class($this->owner)." для настройки ".$name.
                    " не найден элемент списка для редактирования значения настройки (itemid={$option}");
            }
        }else
        {// недопустимый тип данных
            throw new CException('В модели "'.get_class($this->owner)." для настройки ".$name.
                " не найден элемент списка для редактирования значения настройки (itemid={$option}");
        }
        if ( $config->isMultiple() AND $config->valuetype === 'EasyList' AND ! $config->selectedList )
        {// в настройке отсутствует список выбранных значений
            // @todo предусмотреть действия для настройки с одинарным значением
            throw new CException('В модели "'.get_class($this->owner)." для настройки ".$name.
                " не создан список выбранных значений");
        }
        if ( $config->hasSelectedOption($item) )
        {// значение уже в выбрано - удаляем его
            return $config->deselectOption($item);
        }else
        {// значение уже в выбрано - добавляем его
            return $config->selectOption($item);
        }
    }
    
    /**
     * Установить элемент списка в качестве выбранного значения настройки 
     *
     * @param  string                  $name   - служебное название настройки
     * @param  string|int|EasyListItem $option - изменяемый элемент из списка значений настройки
     * @return bool
     * 
     * @todo доработать для настроек с одним значением
     * @todo создавать пользовательский список если настройка должна содержать список значений
     */
    public function selectConfigOption($name, $option)
    {
        // получаем привязанную настройку модели, если ее нет - то создаем и привязываем
        $config = $this->getEditableConfig($name);
        if ( $config->isMultiple() AND $config->valuetype === 'EasyList' AND ! $config->selectedList )
        {// в настройке отсутствует список выбранных значений
            throw new CException('В модели "'.get_class($this->owner)." для настройки ".$name.
                " для настройки не создан список выбранных значений");
        }
        return $config->selectOption($option);
    }
    
    /**
     * Удалить элемент из списка выбранных значений настройки
     *
     * @param  string                  $name   - служебное название настройки
     * @param  string|int|EasyListItem $option - изменяемый элемент из списка значений настройки
     * @return bool
     * 
     * @todo доработать для настроек с одним значением
     * @todo создавать пользовательский список если настройка должна содержать список значений
     */
    public function deselectConfigOption($name, $option)
    {
        // получаем привязанную настройку модели, если ее нет - то создаем и привязываем
        $config = $this->getEditableConfig($name);
        if ( $config->isMultiple() AND $config->valuetype === 'EasyList' AND ! $config->selectedList )
        {// в настройке отсутствует список выбранных значений
            throw new CException('В модели "'.get_class($this->owner)." для настройки ".$name.
                " для настройки не создан список выбранных значений");
        }
        return $config->deselectOption($option);
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
                'objectid',
                'scopes' => array(
                    'withObjectType' => array($this->getOwnerClass()),
                ),
            ),
        );
    }
    
    /**
     * Именованая группа условий: все настройки, содержащие указаный вариант стандартного значения
     *
     * @param  EasyListItem|int|array $option
     * @return Config
     */
    /*public function withSelectedDefaultOption($option)
     {
     
    }*/
    
    /**
     * Именованая группа условий: все настройки, содержащие указаный вариант пользовательского значения
     *
     * @param  EasyListItem|int|array $option
     * @return Config
     */
    /*public function withSelectedUserOption($option)
     {
     
    }*/
    
    /**
     * Именованая группа условий: все настройки, содержащие указаный вариант пользовательского значения
     *
     * @param  EasyListItem|int|array $option
     * @return Config
     */
    /*public function withUserConfigOption($option)
     {
     
    }*/
}