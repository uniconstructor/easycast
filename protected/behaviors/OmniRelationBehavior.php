<?php
/**
 * Заготовка phpdoc чтобы нормально работал code assist по методам owner-класса при обращении к модели
 * Скопируйте ее в комментарий к AR-классу модели к которому прикрепляется это поведение
 * 
 * Методы класса OmniRelationBehavior:
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
 */

/**
 * Класс для работы с любыми AR-моделями которые ссылаются на другие модели
 * при помощи пары полей objecttype/objectid
 * Присоединяется к прикрепляемой модели 
 * (в той, которая содержит эту пару полей и ссылается на другую модель)
 * Поле objecttype всегда содержит класс модели. 
 * Создаваемые модели не обязательно должны быть привязаны к чему-либо,
 * 
 * Работает в паре c поведением OmniRelatedTargetModelBehavior, которое присоединяется к
 * модели на которую ссылается эта запись через objecttype/objectid
 * 
 * Содержит все проверки при сохранении/удалении записей а также все именованые группы условий поиска
 * 
 * @property CActiveRecord $owner
 * 
 * @see OmniRelationTargetBehavior
 * 
 * @todo проверка наличия нужных полей при присоединении к модели
 * @todo документировать все поля
 * @todo документировать методы
 */
class OmniRelationBehavior extends CActiveRecordBehavior
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
     * @var string - тип связи с объектом, к которому прикрепляется модель
     *               Возможные значения:
     *               - CActiveRecord::BELONGS_TO
     *               - CActiveRecord::HAS_ONE
     */
    public $targetRelationType  = CActiveRecord::BELONGS_TO;
    /**
     * @var array
     */
    public $customRelations    = array();
    /**
     * @var array
     */
    public $customScopes       = array();
    /**
     * @var array
     */
    public $customObjectTypes  = array();
    /**
     * @var string
     */
    public $defaultObjectType;
    /**
     * @var int
     */
    public $defaultObjectId        = 0;
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
     *            ноль в этом поле означает отсутствие ограничений на количество 
     *            прикрепляемых к целевой модели объектов 
     *            (например если нужно разрешить прикреплять любое количество видео к заявке)
     */
    public $maxLinksToTargetObject = 0;
    /**
     * @var array
     * @todo
     */
    public $enabledModels          = array();
    /**
     * @var array
     * @todo
     */
    public $disabledModels         = array();
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
    public $extraKeyFields         = array();
    /**
     * @var bool
     * @todo
     */
    public $allowedEmptyExtraKeys = array();
    
    /**
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
     * Именованая группа условий: получить все настройки прикрепленные к переданной модели
     * Эта функция нужна для обращения к настройкам модели в общем виде
     *
     * @param CActiveRecord $model - модель к которой прикреплены настройки
     *                                или название класса такой модели если мо хотим получить
     *                                базовые настройки для всех моделей этого класса
     * @return CActiveRecord
     */
    public function forModel($model)
    {
        if ( ! is_object($object) )
        {// передана модель целиком
            throw new CException('Не передана модель для составления условия');
        }
        // достаем из модели тип и id
        $objectType = get_class($object);
        $objectId   = $object->id;
        
        return $this->forObject($objectType, $objectId);
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
     * Все записи, которые хотя бы раз связаны с любым из указаных классов моделей
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
     * Именованая группа условий: все записи с указанными id
     *
     * @param  int|array $id - id записи или массив из id записей
     * @return CActiveRecord
     */
    public function withId($id)
    {
        if ( ! $id )
        {// условие не используется
            return $this;
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->getTableAlias(true).'.`id`', $id);
    
        $this->getDbCriteria()->mergeWith($criteria);
    
        return $this;
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
     * Все записи с указаным значением в указанном поле
     *
     * @param  string    $field     - поле в котором ищется значение
     * @param  int|array $values    - значение или список значений, которые нужно найти в поле
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function hasCustomValue($field, $values, $operation='AND')
    {
        if ( ! $values )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).'.`'.$field.'`', $values);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: все модели кроме тех которые содержат хотя бы одно
     * из перечисленных значений внутри указанного поля
     *
     * @param  string    $field     - поле в котором ищется значение
     * @param  int|array $values    - значение или список значений, которые нужно найти в поле
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptCustomValue($field, $values, $operation='AND')
    {
        if ( ! $values )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($objectIds) )
        {
            $objectIds = array($objectIds);
        }
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition($this->owner->getTableAlias(true).'.`'.$field.'`', $values);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
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
     * Создать связь с целевым объектом (к которому прикрепляется модель)
     * опираясь на значения по умолчанию
     * 
     * @return array
     */
    protected function getDefaultTargetRelation()
    {
        // получаем название класса связаной модели из текущего значения модели
        $objectTypeField = $this->objectTypeField;
        // создаем массив с названиями и параметрами реляционной связи
        return array(
            $this->targetRelationName => array(
                $this->targetRelationType,
                $this->owner->$objectTypeField,
                $this->objectIdField,
            ),
        );
    }
}