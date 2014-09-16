<?php

/**
 * Класс для работы с любыми AR-моделями которые могут ссылаться на другие модели
 * при помощи пары полей objecttype/objectid
 * Присоединяется к прикрепляемой модели (в той, которая содержит эту пару полей и ссылается
 * на другую модель)
 * Поле objecttype всегда содержит класс модели. Создаваемые модели могут не привязываться
 * к другим моделям (это необязательно)
 * 
 * Работает в паре c поведением OmniRelatedTargetModelBehavior, которое присоединяется к
 * модели на которую ссылается эта запись через objecttype/objectid
 * 
 * Содержит все проверки при сохранении/удалении записей а также все именованые группы условий поиска
 * 
 * @var CActiveRecord $owner
 * 
 * @todo проверка наличия полей при присоединении
 * @todo документировать все поля
 * @todo документировать методы
 */
class OmniRelatedRecordBehavior extends CActiveRecordBehavior
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
    public $targetRelationName = 'targetObject';
    /**
     * @var string
     */
    public $targeRelationType = CActiveRecord::BELONGS_TO;
    /**
     * @var array
     */
    public $customRelations = array();
    /**
     * @var array
     */
    public $customScopes = array();
    /**
     * @var array
     */
    public $customObjectTypes = array();
    /**
     * @var string
     */
    public $defaultObjectType;
    /**
     * @var int
     */
    public $defaultObjectId = 0;
    /**
     * @var bool
     */
    public $enableEmptyObjectType = false;
    /**
     * @var bool
     */
    public $enableEmptyObjectId   = false;
    /**
     * @var int
     */
    public $maxLinksToTargetObject = 0;
    /**
     * @var array
     */
    public $enabledModels = array();
    /**
     * @var array
     */
    public $disabledModels = array();
    /**
     * @var string
     */
    public $extraKeyField;
    /**
     * @var bool
     */
    public $enableEmptyExtraKey = false;
    
    /**
     * 
     * @return void
     */
    public function init()
    {
        $this->owner->init();
    }
    
    /**
     * @return array
     * 
     * @todo создавать исключение если связи с такими именами в модели уже есть
     */
    public function relations()
    {
        // добавляем к стандартной связи все связи из настроек
        $this->customRelations = CMap::mergeArray($this->getDefaultTargetRelation(), $this->customRelations);
        // получаем существующие связи модели 
        $modelRelations = $this->owner->relations();
        // возвращаем список в котором совмещены связи модели ниши связи через составно внешний ключ
        return CMap::mergeArray($modelRelations, $this->customRelations);
    }
    
    /**
     * Все записи, свзязанные хотя бы с одним из объектов указанного типа
     *  
     * @param string    $objectType - тип объекта (как правило класс модели)
     * @param int|array $objectId   - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
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
     * Все записи, свзязанные хотя бы с одним из объектов указанного типа
     * 
     * @param string    $objectType - тип объекта (как правило класс модели)
     * @param int|array $objectId   - id модели в таблице: 0 для записей относящихся ко всем
     *                                объектам модели одновременно
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
     * @return CActiveRecord
     */
    public function forObject($objectType, $objectId, $operation='AND')
    {
        return $this->forAnyObject($objectType, $objectId, $operation);
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
     * @param string $operation  - как присоединить это условие к остальным? (AND/OR)
     * @return CActiveRecord
     */
    public function forEveryObject($objects, $operation='AND')
    {
        if ( ! $objects )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objects) )
        {
            $objects = array($objects);
        }
        
        $columns         = array();
        $objectTypeField = $this->objectTypeField;
        $objectIdField   = $this->objectIdField;
        foreach ( $objects as $condition )
        {// проверяем и составляем массив с условиями
            if ( is_array($condition) )
            {// массив с параметрами поиска
                if ( ! isset($condition[$objectTypeField]) OR ! isset($condition[$objectIdField]) )
                {// пара objecttype/objectid обязательна
                    throw new CException('Для этого условия пара objecttype/objectid обязательна');
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
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
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
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
     * @return CActiveRecord
     */
    public function exceptObject($objectType, $objectId, $operation='AND')
    {
        return $this->exceptLinkedWithAny($objectType, $objectId, $operation);
    }
    
    /**
     * Все записи, кроме тех которые одновременно свзязанны с каждым из указаных объектов
     * (список объектов может быть разнородным)
     *
     * @param array  $objects    - список объектов (тот же класс что и возможного targetType)
     *                             либо индексированый массив, каждый элемент котрого содержит
     *                             ключи objecttype, objecttd (если имена столбцов в targetModel
     *                             стандартные) а также дополнительно содержать список других
     *                             условий для подстановки в функцию addColumnCondition
     * @param string $operation  - как присоединить это условие к остальным? (AND/OR)
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
     * @param string    $operation   - как присоединить это условие к остальным? (AND/OR)
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
     * Все записи, которые хотя бы раз связаны с любым из указаных классов моделей
     *
     * @param string    $objectTypes - тип объекта (как правило класс модели) или индексированый
     *                                 массив со списком классов моделей
     * @param string    $operation   - как присоединить это условие к остальным? (AND/OR)
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
     * Все записи, кроме тех которые одновременно свзязанны с каждым из указаных типов объекта
     * (список объектов может быть разнородным)
     *
     * @param array  $objectTypes - тип объекта (как правило класс модели) или индексированый
     *                              массив со списком классов моделей
     * @param string $operation  - как присоединить это условие к остальным? (AND/OR)
     * @return CActiveRecord
     * 
     * @todo не отличается от withAnyObjectType - удалить
     */
    /*
    public function withEveryObjectType($objectTypes, $operation='AND')
    {
        
    }
    public function withEveryObjectId($objectIds, $operation='AND')
    {
    
    }
    */
    
    /**
     * Все записи, которые хотя бы раз связаны с любым из переданых id (независимо от типа)
     *
     * @param int|array $objectIds - id модели в таблице: 0 для записей относящихся ко всем
     *                               объектам модели одновременно, или индексированый масив
     *                               таких id
     * @param string    $operation - как присоединить это условие к остальным? (AND/OR)
     * @return CActiveRecord
     */
    public function withAnyObjectId($objectIds, $operation='AND')
    {
        if ( ! $objectIds )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).'.`'.$this->objectIdField.'`', $objectIds);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, которые хотя бы раз связаны с любым из переданых id (независимо от типа)
     *
     * @param int|array $objectIds - id модели в таблице: 0 для записей относящихся ко всем
     *                               объектам модели одновременно, или индексированый масив
     *                               таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
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
     * @param string       $operation  - как присоединить это условие к остальным? (AND/OR)
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
     * @param int|array $objectIds - id модели в таблице: 0 для записей относящихся ко всем
     *                               объектам модели одновременно, или индексированый масив
     *                               таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
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
        
        // итоговое условие требует одновременного выполнения условий 
        // для каждого переданного типа в отличии от IN
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition($columns, 'AND', 'AND');
        
        $this->owner->getDbCriteria()->mergeWith($criteria,  $operation.' NOT ');
        
        return $this->owner;
    }
    
    /**
     * Все записи, кроме тех, которые хотя бы раз связаны хотя бы с одним из id
     * объектов в списке (без учета типа)
     *
     * @param int|array $objectIds - id модели в таблице: 0 для записей относящихся ко всем
     *                               объектам модели одновременно, или индексированый масив
     *                               таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
     * @return CActiveRecord
     */
    public function exceptLinkedWithAnyObjectId($objectIds, $operation='AND')
    {
        if ( ! $objectIds )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objectIds) )
        {
            $objectIds = array($objectIds);
        }
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition($this->owner->getTableAlias(true).
            '.`'.$this->objectIdField.'`',  $objectIds);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, кроме тех, которые хотя бы раз связаны с каждым из id
     * объектов в списке (без учета типа)
     *
     * @param int|array $objectIds - id модели в таблице: 0 для записей относящихся ко всем
     *                               объектам модели одновременно, или индексированый масив
     *                               таких id
     * @param string    $operation  - как присоединить это условие к остальным? (AND/OR)
     * @return CActiveRecord
     */
    public function exceptLinkedWithEveryObjectId($objectIds, $operation='AND')
    {
        if ( ! $objectIds )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objectIds) )
        {
            $objectIds = array($objectIds);
        }
        
        $columns = array();
        foreach ( $objectIds as $objectId )
        {// для каждого id создаем условие сравнения
            $columns[] = array(
                $this->owner->getTableAlias(true).".`{$this->objectIdField}`" => $objectId,
            );
        }
        
        // итоговое условие требует одновременного выполнения условий
        // для каждого переданного типа в отличии от IN
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition($columns, 'AND', 'AND');
        
        $this->owner->getDbCriteria()->mergeWith($criteria,  $operation.' NOT ');
        
        return $this->owner;
    }
    
    /**
     * 
     * @return array
     */
    protected function getDefaultTargetRelation()
    {
        // получаем название класса связаной модели из текущего значения модели
        $objectTypeField = $this->$objectTypeField;
        $objectType      = $this->owner->$objectTypeField;
        
        return array(
            $this->targetRelationName => array(
                $this->targetRelationType,
                $this->owner->$objectTypeField,
                $this->objectIdField,
            ),
        );
    }
}

/*$columns         = array();
 $objectTypeField = $this->objectTypeField;
$objectIdField   = $this->objectIdField;
foreach ( $objects as $condition )
{// проверяем и составляем массив с условиями
if ( is_array($condition) )
{// массив с параметрами поиска
if ( ! isset($condition[$objectTypeField]) OR ! isset($condition[$objectIdField]) )
{// пара objecttype/objectid обязательна
throw new CException('Для этого условия пара objecttype/objectid обязательна');
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
foreach ( $columns as $column )
{
foreach ( $column as $field => $value )
{

}
}*/