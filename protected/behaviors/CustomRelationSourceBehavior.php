<?php
/**
 * Заготовка phpdoc чтобы нормально работал code assist по методам owner-класса при обращении к модели
 * Скопируйте ее в комментарий к AR-классу модели к которому прикрепляется это поведение
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
 * @method CActiveRecord withCustomValue(string $field, array|string $values, string $operation='AND')
 * @method CActiveRecord exceptCustomValue(string $field, array|string $values, string $operation='AND')
 * @method bool          isUniqueForObject(string $objectType, int $objectId=0, int $extraKeyId=0)
 */

/**
 * Класс со списком условий поиска для моделей которые используют составной внешний ключ (objecttype/objectid) 
 * Работает с любыми AR-моделями которые используют для составного ключа поля "objecttype" (класс модели)
 * и "objectid" (первичный ключ модели)
 * Присоединяется к модели которая содержит эту пару полей
 * 
 * Создаваемые записи не обязательно должны быть привязаны к существующей модели 
 * (см. настройки поведения)
 * 
 * Работает в паре c поведением OmniRelatedTargetModelBehavior: оно присоединяется "с другой стороны связи",
 * то есть к модели НА Ккоторую ссылается эта запись содержащая пару objecttype/objectid
 * Такая модель называется target-моделью
 * 
 * Содержит все проверки при сохранении/удалении записей а также именованые группы условий для поиска
 * Подключение этого поведения сильно сокращает количество дублируемого кода в моделях с составным 
 * внешним ключом
 * 
 * @property CActiveRecord $owner
 * 
 * @see CustomRelationTargetBehavior
 * @see http://www.yiiframework.com/doc/guide/1.1/en/database.ar#named-scopes
 * 
 * @todo проверка наличия нужных полей при присоединении к модели
 * @todo документировать все поля
 * @todo документировать методы
 * @todo scopes()
 * @todo тесты для всех методов этого класса
 * @todo переименовать в CustomRelationSourceBehavior
 * @todo придумать каким образом можно автоматически подключать CustomScopesBehavior 
 *       при подключении этого поведения (пока используем наследование)
 * @todo $this->updateTargetObject($field, $value)
 */
class CustomRelationSourceBehavior extends CustomScopesBehavior
{
    /**
     * @var string
     */
    public $objectTypeField    = 'objecttype';
    /**
     * @var string
     */
    public $objectIdField      = 'objectid';
    /**
     * @var string
     */
    public $targetRelationName = 'customRelationTarget';
    /**
     * @var string - класс модели, используемый в связи по умолчанию в том случае когда 
     *               $this->owner->objecttype не задан
     *               Нужен для того чтобы можно было использовать группы условий поиска
     *               даже тогда, когда в модель еще не загружены данные 
     */
    public $defaultTargetType;
    /**
     * @var int    - id модели, используемый в связи по умолчанию в том случае когда 
     *               $this->owner->objectid не задан
     *               Нужен для того чтобы можно было использовать группы условий поиска
     *               даже тогда, когда в модель еще не загружены данные
     */
    public $defaultTargetId;
    /**
     * @var string - тип связи с объектом, к которому прикрепляется модель
     *               Возможные значения:
     *               - CActiveRecord::BELONGS_TO
     *               - CActiveRecord::HAS_ONE
     */
    public $targetRelationType = CActiveRecord::BELONGS_TO;
    /**
     * @var array - массив с дополнительными связями (relations) для owner-модели
     */
    public $customRelations    = array();
    /**
     * @var array
     */
    //public $customScopes       = array();
    /**
     * @var array
     */
    public $customObjectTypes  = array();
    /**
     * @var string - стандартное значение для objecttype (если не задано)
     *               используется при создании новой модели
     */
    //public $defaultObjectType;
    /**
     * @var int - стандартное значение для objectid (если не задано)
     *            используется при создании новой модели
     */
    //public $defaultObjectId        = 0;
    /**
     * @var bool
     */
    public $enableEmptyObjectType  = false;
    /**
     * @var bool
     */
    public $enableEmptyObjectId    = false;
    /**
     * @var int - максимальное количество прикрепляемых к целевой модели объектов
     *            Ноль в этом поле означает отсутствие ограничений на количество 
     *            прикрепляемых к target-модели объектов 
     *            (например если нужно разрешить прикреплять любое количество комментариев к заявке)
     */
    public $maxLinksToTargetObject = 0;
    /**
     * @var array - список моделей на которые записи могут ссылаться по objecttype/objectid
     * 
     * @todo автоматически создавать по одной связи для каждого класса из этого списка
     */
    public $enabledModels          = array();
    /**
     * @var array
     * @todo
     */
    //public $disabledModels         = array();
    /**
     * @var array - список полей для дополнительных внешних ключей с указанием того, 
     *              допустимо ли нулевое значение для этого поля.
     *              Индексы в этом массиве это названия полей в owner-модели а значения
     *              указывают допустим ли ноль в этом поле 
     *              array(
     *                  'myexternalid'   => true,
     *                  'myexternaltype' => true,
     *                  ...
     *              ),
     *              ...
     * @todo
     */
    //public $extraKeyFields         = array();
    /**
     * @var bool
     * @todo
     */
    //public $allowedEmptyExtraKeys  = array();
    
    /**
     * @param  string $name
     * @return void
     */
    public function setDefaultTargetType($type)
    {
        $this->defaultTargetType = $type;
    }
    
    /**
     * @return string
     */
    public function getDefaultTargetType()
    {
        if ( ! $this->defaultTargetType )
        {
            $this->defaultTargetType = $this->owner->getAttribute($this->objectTypeField);
        }
        return $this->defaultTargetType;
    }
    
    /**
     * @param  string $name
     * @return void
     */
    public function setDefaultTargetId($id)
    {
        $this->defaultTargetId = $id;
    }
    
    /**
     * @return string
     */
    public function getDefaultTargetId()
    {
        if ( ! $this->defaultTargetId )
        {
            $this->defaultTargetId = $this->owner->getAttribute($this->objectIdField);
        }
        return $this->defaultTargetId;
    }
    
    /**
     * Добавить в owner-модель связь с target-объектом
     * (чтобы по его полям можно было искать записи из этой таблицы)
     * Эта функция вызывается перед выполнением поиска и проверяет, подгружена ли в owner-модель
     * нужная связь и если нет - то добавляет ее, обновляя метаданные owner-модели
     * 
     * @param  string $objectType   - класс модели для создания связи
     * @param  string $relationName - название связи
     * @return void
     */
    public function initTargetRelation($objectType=null, $relationName=null)
    {
        if ( ! $objectType AND ! $objectType = $this->getDefaultTargetType() )
        {// недостаточно данных для создания связи
            throw new CException('Невозможно задать условие поиска по target-модели: 
                в owner-модель не загружен составной ключ (objecttype/objectid). 
                Для использования условий поиска из поведения '.get_class($this).
                ' попробуйте использовать модель с данными [$modelObject->forObject(...)] 
                вместо пустой модели [ModelClass::model()->forObject(...)]');
        }
        if ( ! $relationName )
        {
            $relationName = $this->targetRelationName; // 'related'.$objectType
        }
        if ( ! $this->owner->hasRelated($relationName) )
        {// добавляем связь к модели (если ее там еще нет)
            $this->owner->addCustomRelation($relationName, current($this->getCustomTargetRelation($objectType)));
        }
    }
    
    /**
     * @see CBehavior::attach()
     */
    public function attach($owner)
    {
        parent::attach($owner);
        // добавляем к стандартной связи все указанные в параметрах поведения при подключении модели
        $this->customRelations = CMap::mergeArray($this->getDefaultTargetRelation(), $this->customRelations);
        if ( $this->customRelations )
        {// после присоединения к модели добавляем в нее дополнительные связи
            $this->owner->addCustomRelations($this->customRelations);
        }
    }
    
    /**
     * @see CActiveRecordBehavior::afterFind()
     */
    public function afterFind($event)
    {
        parent::afterFind($event);
        // получаем objecttype для построения условий поиска
        $type = $this->owner->getAttribute($this->objectTypeField);
        $this->initTargetRelation($type);
    }
    
    /**
     * Именованая группа условий: получить все настройки прикрепленные к переданной модели
     * Эта функция используется для обращения к настройкам модели в общем виде
     * Если передана несохраненная модель или модель не содержащая id - то поведение функции
     * будет зависеть от параметра {@see self::enableEmptyObjectId} 
     * - если разрешен нулевой objectid то будет получена модель с objectid=0 
     *   (относящаяся ко всем моделям класса одновременно)
     * - если нулевой objectid запрещен - то будет создано исключение
     *
     * @param  CActiveRecord $model - модель к которой прикреплены настройки
     *                                или название класса такой модели если мо хотим получить
     *                                базовые настройки для всех моделей этого класса
     * @param  string $operation    - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     * @throws CException
     */
    public function forModel($model, $operation='AND')
    {
        if ( ! is_object($model) OR ! isset($model->id) )
        {// передана модель целиком
            throw new CException('Не передана модель для составления условия');
        }
        // достаем из модели тип и id
        $objectType = get_class($model);
        $objectId   = $model->id;
        
        if ( ! $this->isModel($objectType) )
        {// проверяем что переданный объект является AR-моделью
            throw new CException('Переданный объект не является записью Active Record');
        }
        if ( ! $model->id AND ! $this->enableEmptyObjectId AND YII_DEBUG )
        {// выбрасываем исключение только в режиме отладки
            throw new CException('К модели '.$objectType.' запрещено привязывать записи '.
                get_class($this->owner).' без указания objectid. Проверьте вызов условия поиска.');
        }
        return $this->forObject($objectType, $objectId, $operation);
    }
    
    /**
     * Все записи, свзязанные хотя бы с одним из объектов указанного типа
     *
     * @param string    $objectType - тип объекта (как правило класс модели)
     * @param int|array $objectId   - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function forObject($objectType, $objectId, $operation='AND')
    {
        return $this->forAnyObject($objectType, $objectId, $operation);
    }
    
    /**
     * Все записи, свзязанные хотя бы с одним из объектов указанного типа
     *  
     * @param string    $objectType - тип объекта (как правило класс модели)
     * @param int|array $objectId   - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function forAnyObject($objectType, $objectId, $operation='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).'.`'.$this->objectTypeField.'`', $objectType);
        $criteria->compare($this->owner->getTableAlias(true).'.`'.$this->objectIdField.'`', $objectId);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, свзязанные с каждым из объектов одновременно
     * (список объектов может быть разнородным)
     *
     * @param array  $objects    - список объектов (тот же класс что и $this->owner)
     *                             либо индексированый массив, каждый элемент котрого содержит
     *                             ключи objecttype, objectid (если имена столбцов в targetModel
     *                             стандартные) а также дополнительно содержать список других
     *                             полей модели $this->owner для подстановки 
     *                             в функцию addColumnCondition
     * @param string $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function forEveryObject($objects, $operation='AND')
    {
        $columns         = array();
        $objectTypeField = $this->objectTypeField;
        $objectIdField   = $this->objectIdField;
        if ( ! $objects )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objects) )
        {
            $objects = array($objects);
        }
        foreach ( $objects as $condition )
        {// проверяем и составляем массив с условиями
            if ( is_array($condition) )
            {// массив с параметрами поиска
                if ( empty($condition) )
                {// пропускаем пустые условия
                    continue;
                }
                $columns[] = $condition;
            }elseif ( is_object($condition) )
            {// модель с параметрами поиска: берем из нее только objecttype/objectid
                if ( ! isset($condition->$objectTypeField) OR ! isset($condition->$objectIdField) )
                {// пара objecttype/objectid обязательна
                    throw new CException('Для этого условия пара objecttype/objectid обязательна');
                }
                $columns[] = array(
                    $objectTypeField => $condition->$objectTypeField,
                    $objectIdField   => $condition->$objectIdField,
                );
            }else
            {
                throw new CException('Список параметров для этого условия поиска должен быть
                    массивом c параметрами для addColumnCondition()');
            }
        }
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition($columns, 'AND', $operation);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, кроме связанных хотя бы с одним из объектов указанного типа
     *
     * @param string    $objectType - тип объекта (как правило класс модели)
     * @param int|array $objectId   - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptLinkedWithAny($objectType, $objectId, $operation='AND')
    {
        if ( ! is_array($objectId) )
        {// для NOT IN обязательно нужен массив
            $objectId = array($objectId);
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).'.`'.$this->objectTypeField.'`', $objectType);
        $criteria->addNotInCondition($this->owner->getTableAlias(true).
            '.`'.$this->objectIdField.'`', $objectId);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, кроме связанных хотя бы с одним из объектов указанного типа
     *
     * @param string    $objectType - тип объекта (как правило класс модели)
     * @param int|array $objectId   - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptLinkedWith($objectType, $objectId, $operation='AND')
    {
        return $this->exceptLinkedWithAny($objectType, $objectId, $operation);
    }
    
    /**
     * Все записи, кроме тех которые одновременно свзязанны с каждым из указаных объектов
     * (список объектов может быть разнородным, то есть содержать модели разных классов)
     *
     * @param array  $objects    - список объектов (тот же класс что и возможного targetType)
     *                             либо индексированый массив, каждый элемент котрого содержит
     *                             ключи objecttype, objecttd (если имена столбцов в targetModel
     *                             стандартные) а также дополнительно содержать список других
     *                             условий для подстановки в функцию addColumnCondition
     * @param string $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptLinkedWithEvery($objects, $operation='AND')
    {
        if ( ! $objects )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objects) )
        {
            $objects = array($objects);
        }
        // то же условие что и раньше, только исключаем из выборки записи вместо добавления в нее
        return $this->forEveryObject($objects, $operation.' NOT ');
    }
    
    /**
     * Все записи, которые хотя бы раз связаны с любым из указаных классов моделей
     *
     * @param string    $objectTypes - тип объекта (как правило класс модели) или индексированый
     *                                 массив со списком классов моделей
     * @param string    $operation   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withAnyObjectType($objectTypes, $operation='AND')
    {
        if ( ! $objectTypes )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).'.`'.$this->objectTypeField.'`', $objectTypes);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, которые хотя бы раз связаны с любым из указаных классов моделей (alias)
     *
     * @param string    $objectTypes - тип объекта (как правило класс модели) или индексированый
     *                                 массив со списком классов моделей
     * @param string    $operation   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withObjectType($objectTypes, $operation='AND')
    {
        if ( ! $objectTypes )
        {// условие не используется
            return $this->owner;
        }
        return $this->withAnyObjectType($objectTypes, $operation);
    }
    
    /**
     * Все записи, которые хотя бы раз связаны с любым из переданых id (независимо от типа)
     *
     * @param int|array $objectIds - id модели в таблице: 0 для записей относящихся ко всем
     *                               объектам модели одновременно, или индексированый масив таких id
     * @param string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withAnyObjectId($objectIds, $operation='AND')
    {
        if ( ! $objectIds )
        {// поиск по objectid=0
            $objectIds = intval($objectIds);
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).'.`'.$this->objectIdField.'`', $objectIds);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * (alias) Все записи, которые хотя бы раз связаны с любым из переданых id (независимо от типа)
     *
     * @param int|array $objectIds  - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно, или индексированый масив таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withObjectId($objectIds, $operation='AND')
    {
        return $this->withAnyObjectId($objectIds, $operation);
    }
    
    /**
     * Все записи, кроме тех, которые хотя бы раз связаны хотя бы с одним из типов
     * моделей в списке
     *
     * @param string|array $objectTypes- тип объекта (как правило класс модели)
     * @param string       $operation  - как присоединить это условие к остальным?
     *                                   (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptLinkedWithAnyObjectType($objectTypes, $operation='AND')
    {
        if ( ! $objectTypes )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objectTypes) )
        {
            $objects = array($objects);
        }
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition($this->owner->getTableAlias(true).
            '.`'.$this->objectTypeField.'`', $objectTypes);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, кроме тех, которые хотя бы по одному разу связаны с каждым из типов 
     * моделей в списке
     *
     * @param string    $objectType - тип объекта (как правило класс модели)
     * @param int|array $objectIds  - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно, или индексированый масив таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptLinkedWithEveryObjectType($objectTypes, $operation='AND')
    {
        if ( ! $objectTypes )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objectTypes) )
        {
            $objects = array($objects);
        }
        
        $columns = array();
        foreach ( $objectTypes as $objectType )
        {// для каждого типа создаем условие сравнения
            $columns[] = array(
                $this->owner->getTableAlias(true).".`{$this->objectTypeField}`" => $objectType,
            );
        }
        // итоговое условие требует одновременной связки с каждым переданным типом (в отличии от IN)
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition($columns, 'AND', 'AND');
        
        $this->owner->getDbCriteria()->mergeWith($criteria,  $operation.' NOT ');
        
        return $this->owner;
    }
    
    /**
     * (alias) Все записи, кроме тех, которые хотя бы раз связаны хотя бы с одним из id
     * объектов в списке (без учета типа)
     *
     * @param int|array $objectIds  - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно, или индексированый масив таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptObjectId($objectIds, $operation='AND')
    {
        return $this->exceptLinkedWithAnyObjectId($objectIds, $operation);
    }
    
    /**
     * Все записи, кроме тех, которые хотя бы раз связаны хотя бы с одним из id
     * объектов в списке (без учета типа)
     *
     * @param int|array $objectIds  - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно, или индексированый масив таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptLinkedWithAnyObjectId($objectIds, $operation='AND')
    {
        if ( ! $objectIds )
        {// поиск по objectid=0
            $objectIds = intval($objectIds);
        }
        if ( ! is_array($objectIds) )
        {
            $objectIds = array($objectIds);
        }
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition($this->owner->getTableAlias(true).
            '.`'.$this->objectIdField.'`', $objectIds);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, кроме тех, которые хотя бы раз связаны с каждым из id
     * объектов в списке (без учета типа)
     *
     * @param int|array $objectIds  - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно, или индексированый масив таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptLinkedWithEveryObjectId($objectIds, $operation='AND')
    {
        $columns = array();
        if ( ! $objectIds )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objectIds) )
        {
            $objectIds = array($objectIds);
        }
        foreach ( $objectIds as $objectId )
        {// для каждого id создаем по одному условию строгого сравнения
            $columns[] = array(
                $this->owner->getTableAlias(true).".`{$this->objectIdField}`" => $objectId,
            );
        }
        // итоговое условие требует одновременной связки с каждым переданным id (в отличии от IN)
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition($columns, 'AND', 'AND');
        
        $this->owner->getDbCriteria()->mergeWith($criteria,  $operation.' NOT ');
        
        return $this->owner;
    }
    
    /**
     * Определить, является ли текущая owner-модель единственной моделью
     * которая ссылается на объект с таким objecttype/objectid
     * 
     * @param  string $objectType - тип объекта (как правило класс модели)
     * @param  number $objectId   - id модели
     * @param  number $extraKeyId - id дополнительного внешнего ключа (если есть)
     * @return bool
     * 
     * @todo дописать логику для проверки дополнительного ключа и придумать что делать
     *       если дополнительных внешних ключей несколько
     */
    public function isUniqueForObject($objectType, $objectId=0, $extraKeyId=0)
    {
        if ( $this->forObject($objectType, $objectId)->count() > 1 )
        {
            return false;
        }
        return true;
    }
    
    /**
     * Получить объект на который ссылается эта запись
     * 
     * @return CActiveRecord
     */
    public function getTargetObject()
    {
        $modelClass = $this->getDefaultTargetType();
        $id         = $this->getDefaultTargetId();
        
        if ( ! $modelClass OR ! $this->isModel($modelClass) )
        {// класс модели не указан или тип объекта не является классом модели
            return null;
        }
        return $modelClass::model()->findByPk($id);
    }
    
    /**
     * Обновить привязанный к элементу списка объект
     *
     * @param  string $field
     * @param  string $value
     * @return bool
     *
     * @todo добавлять возникшие при сохранении ошибки к ошибкам этой модели, в поле, 
     *       указанное в настройках поведения
     */
    public function updateTargetObject($field, $value)
    {
        if ( ! $target = $this->getTargetObject() )
        {
            return false;
        }
        $target->$field = $value;
         
        return $target->save();
    }
    
    /**
     * Проверить содержит ли поле модели objectType
     *
     * @param  string|Object $objectType - тип объекта к которому привязана модель
     * @return bool
     */
    protected function isModel($objectType)
    {
        if ( ! class_exists($objectType, false) OR ! is_subclass_of($objectType, 'CActiveRecord') )
        {// такой класс не существует или не является классом модели
            return false;
        }
        if ( in_array($objectType, $this->customObjectTypes) )
        {// тип объекта в специальном списке
            return false;
        }
        return true;
    }
    
    /**
     * Создать связь с целевым объектом (к которому прикрепляется модель)
     * опираясь на значения по умолчанию
     * 
     * @return array
     */
    protected function getDefaultTargetRelation()
    {
        return $this->getCustomTargetRelation();
    }
    
    /**
     * Создать связь с целевым объектом (к которому прикрепляется модель)
     * опираясь на значения по умолчанию
     * 
     * @return array
     */
    protected function getCustomTargetRelation($objectType=null)
    {
        if ( ! $objectType )
        {
            $objectType = $this->getDefaultTargetType();
        }
        // создаем массив с названиями и параметрами реляционной связи
        return array(
            $this->targetRelationName => array(
                $this->targetRelationType,
                $objectType,
                $this->objectIdField,
            ),
        );
    }
}