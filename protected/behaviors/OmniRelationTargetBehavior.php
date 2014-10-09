<?php

/**
 * Это поведение для "target-моделей" к которым через связку objecttype/objectid прикрепляются 
 * другие модели  
 * Модель с этим поведением может не иметь полей objecttype/objectid: она является главной,
 * а все прикрепленные к ней записи - дочерними
 * 
 * Поведение содержит все HAS_MANY связи с дочерними объектами
 * Поведение может присоединятся к одному AR-классу несколько раз, в том числе несколько 
 * раз для одной и той же дочерней модели (для разных сязей сделаных через одну таблицу)
 * 
 * Названия функций поиска по дочерним объектам должны отличаться от названий функций поиска 
 * по objecttype/objectid в классе OmniRelationBehavior потому что две модели могут одновременно 
 * ссылаться друг на друга, а значит использовать оба класса поведения
 * Для того чтобы избежать конфликта имен методов в такой ситуации используем правило:
 * все функции поиска по связанным (дочерним) объектам содержат в названии слова link или linked
 * 
 * Одно подключенное поведение - одна связь
 * 
 * Пример:
 * OmniRelationBehavior::forObject() 
 *     найти все записи которые ссылаются на указанный объект
 *     (например всех участников события)
 * OmniRelationTargetBehavior::withLinkFromObject() 
 *     найти все записи на которые ссылается указанный объект
 *     (обратная операция: например все события участника)
 *     
 * TL;DR: (наглядный пример) 
 *        OmniRelationBehavior связывает пользователей с событиями а 
 *        OmniRelationTargetBehavior крепится к событиям чтобы ссылаться на пользователей 
 *        "с другой стороны". Два эти поведения позволяют искать пользователей по событиям и 
 *        события по пользователям.
 * 
 * @property CActiveRecord $owner
 * 
 * @see OmniRelationBehavior
 * 
 * @todo автопроверки существования relation-связей при присоединении к классу owner-модели
 * @todo тесты для всех методов этого класса
 */
class OmniRelationTargetBehavior extends CActiveRecordBehavior
{
    /**
     * @var string
     */
    public $linkObjectTypeField      = 'objecttype';
    /**
     * @var string
     */
    public $linkObjectIdField        = 'objectid';
    /**
     * @var string - название HAS_MANY связи в owner-модели которая содержит все записи которые
     *               ссылаются сюда по objecttype/objectid
     */
    public $linkedModelsRelationName = 'linkedModels';
    /**
     * @var string - обязательный параметр: класс AR-моделей которые ссылаются сюда 
     *               (то есть на эту owner-модель: модель к которой прикреплено это поведение)
     *               модели которые ссылаются сюда должны использовать OmniRelationBehavior
     */
    public $linkedModelsClass;
    /**
     * @var array
     */
    public $linkedModelsRelation = array();
    
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
        // получаем существующие связи модели и возвращаем список
        $this->linkedModelsRelation = $this->getDefaultLinkedModelsRelation();
        // в котором совмещены связи модели наши связи с дочерними объектами
        return CMap::mergeArray($this->owner->relations(), $this->linkedModelsRelation);
    }
    
    /**
     * Условие поиска: получить все записи на которые ссылается указаный объект 
     * (или хотя бы один объект из списка id)
     * 
     * @param  string    $objectType - тип дочерней модели которая ссылается сюда
     * @param  array|int $objectId   - id дочерней модели которая ссылается сюда
     * @param  string    $operator   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withLinkFromObject($objectType, $objectId=0, $operator='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->linkedModelsRelationName => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'forObject' => array($objectType, $objectId),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operator);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: получить все записи на которые ссылается хотя бы один из переданых объектов
     * (достаточно связи хотя бы с одним объектом из $objects)
     *
     * @param  array|int $objects  - список объектов которые должны быть связаны с owner-моделью
     * @param  string    $operator - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     * 
     * @todo
     */
    /*public function withLinkFromAny($objects, $operator='AND')
    {
        
    }
    
    /**
     * Условие поиска: получить все записи на которые ссылается каждый из переданных объектов
     * (то есть если хотя бы один объект из списка сюда не ссылается - то условие не выполнено)
     *
     * @param  array|int $objects  - список объектов которые должны быть связаны с owner-моделью
     * @param  string    $operator - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withLinkFromEvery($objects, $operator='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->linkedModelsRelationName => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'forEveryObject' => array($objects),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operator);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: получить все записи кроме тех на которые ссылается хотя бы один переданный объект
     * 
     * @param  string    $objectType - тип дочерней модели которая ссылается сюда
     * @param  array|int $objectId   - id дочерней модели которая ссылается сюда
     * @param  string    $operator   - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptHavingAnyLinkFrom($objectType, $objectId, $operator='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->linkedModelsRelationName => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'exceptLinkedWith' => array($objectType, $objectId),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operator);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: получить все записи кроме тех на которые ссылается каждый переданный объект
     * 
     * @param  array|int $objects  - список объектов которые должны быть связаны с owner-моделью
     * @param  string    $operator - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptHavingEveryLinkFrom($objects, $operator='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->linkedModelsRelationName => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'exceptLinkedWithEvery' => array($objects),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operator);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: все записи, на которые ссылаются модели определенного класса (alias)
     *
     * @param  string|array $linkTypes - тип ссылок на эту модель (как правило тоже класс модели)
     *                                   или индексированый массив со списком классов моделей
     * @param  string       $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withLinkFromType($linkTypes, $operation='AND')
    {
        return $this->withLinkFromAnyType($linkTypes, $operation);
    }
    
    /**
     * Условие поиска: все записи, на которые ссылаются модели определенного класса
     *
     * @param  string|array $linkTypes - тип ссылок на эту модель (как правило тоже класс модели) 
     *                                   или индексированый массив со списком классов моделей
     * @param  string       $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withLinkFromAnyType($linkTypes, $operation='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->linkedModelsRelationName => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'withAnyObjectType' => array($linkTypes),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operator);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: все записи, на которые одновременно ссылаются несколько моделей разных классов
     *
     * @param  string|array $linkTypes - тип ссылок на эту модель (как правило тоже класс модели) 
     *                                   или индексированый массив со списком классов моделей
     * @param  string       $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     * 
     * @todo
     */
    public function withLinkFromEveryType($linkTypes, $operation='AND')
    {
        if ( ! $linkTypes )
        {// условие не используется
            return $this->owner;
        }
        if ( ! is_array($linkTypes) )
        {// нормализуем входные данные перед поиском
            $linkTypes = array($linkTypes);
        }
        $linkTypes = array_unique($linkTypes);
        
        $criteria = new CDbCriteria();
        $criteria->together = true;
        foreach ( $linkTypes as $linkType )
        {// для каждого из типа связей добавляем обязательное условие существования 
            // связанной записи с определенным objecttype
            $linkCriteria = new CDbCriteria();
            $linkCriteria->together = true;
            $linkCriteria->with = array(
                $this->linkedModelsRelationName => array(
                    'joinType' => 'INNER JOIN',
                    'scopes'   => array(
                        'withAnyObjectType' => array($linkType),
                    ),
                ),
            );
            $criteria->mergeWith($linkCriteria, 'AND');
        }
        $this->owner->getDbCriteria()->mergeWith($criteria, $operator);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: все записи, на которые ссылается хотя бы одна модель c id из переданного списка (alias)
     *
     * @param  array|int $linkIds   - id ссылок на эту модель
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withLinkFromId($linkIds, $operation='AND')
    {
        return $this->withLinkFromAnyId($linkIds, $operation);
    }
    
    /**
     * Условие поиска: все записи, на которые ссылается хотя бы одна модель c id из переданного списка
     *
     * @param  array|int $linkIds   - id ссылок на эту модель
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withLinkFromAnyId($linkIds, $operation='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->linkedModelsRelationName => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'withId' => array($linkIds),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operator);
        
        return $this->owner;
    }
    
    /**
     * Условие поиска: все записи, на которые одновременно ссылаются все модели c id из переданного списка
     *
     * @param  array|int $linkIds   - id ссылок на эту модель 
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     * 
     * @todo
     */
    /*public function withLinkFromEveryId($linkIds, $operation='AND')
    {
        
    }*/
    
    /**
     * Условие поиска: все записи, кроме тех на которые ссылаются модели определенного класса (хотя бы одна)
     *
     * @param  string|array $linkTypes - тип ссылок на эту модель (как правило тоже класс модели)
     *                                   или индексированый массив со списком классов моделей
     * @param  string       $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     * 
     * @todo возможно не имеет смысла - дочерние связанные объекты будут иметь строго определенный objecttype 
     */
    /*public function exceptHavingAnyLinkFromType($linkTypes, $operation='AND')
    {
        
    }*/
    
    /**
     * Условие поиска: все записи, кроме тех на которые ссылаются модели определенного с определенным id
     *
     * @param  array|int $linkIds   - id ссылок на эту модель
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptHavingAnyLinkId($linkIds, $operation='AND')
    {
        return $this->withLinkFromAnyId($linkIds, 'NOT '.$operation);
    }
    
    /**
     * Условие поиска: все записи, кроме тех на которые ссылаются модели определенного с определенным objectid
     *
     * @param  array|int $linkIds   - id ссылок на эту модель
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     * 
     * @todo возможно не имеет смысла - дочерние связанные объекты будут иметь строго определенный linkid
     */
    /*public function exceptHavingAnyLinkObjectId($linkIds, $operation='AND')
    {
        
    }*/
    
    /**
     * Условие поиска: все записи с указанными id
     * 
     * @param  int|array $id - id записи или массив из id записей
     * @return CActiveRecord
     */
    public function withId($id)
    {
        if ( ! $id )
        {// условие не используется
            return $this->owner;
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->getTableAlias(true).'.`id`', $id);
    
        $this->owner->getDbCriteria()->mergeWith($criteria);
    
        return $this->owner;
    }
    
    /**
     * Создать связь с целевым объектом (к которому прикрепляется модель)
     * опираясь на значения по умолчанию
     *
     * @return array
     */
    protected function getDefaultLinkedModelsRelation()
    {
        // получаем название класса связаной модели из текущего значения модели
        $objectTypeField = $this->linkObjectTypeField;
        // создаем массив с названиями и параметрами связи
        return array(
            $this->linkedModelsRelationName => array(
                CActiveRecord::HAS_MANY,
                $this->linkedModelsClass,
                $this->linkObjectIdField,
                'scopes' => array(
                    'forObject' => array(get_class($this->owner), $this->owner->id),
                ),
            ),
        );
    }
}