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
 * Пример:
 * OmniRelationBehavior::forObject() 
 *     найти все записи которые ссылаются на указанный объект
 *     (например всех участников события)
 * OmniRelationTargetBehavior::withLinkFromObject() 
 *     найти все записи на которые ссылается указанный объект
 *     (обратная операция: например все события участника)
 * 
 * Одно подключенное поведение должно отвечать за все связи с дочерними моделями.
 * Например, если на модель Questionary ссылаются модели EasyListItem и Video, то 
 * подключая это поведение к Questionary нужно описать в его настройках две связи,
 * но не подключать это поведение два раза с разными настройками
 * 
 * 
 * TL;DR: (пример) 
 *        OmniRelationBehavior связывает пользователей с событиями а 
 *        OmniRelationTargetBehavior крепится к событиям чтобы ссылаться на пользователей 
 *        "с другой стороны". Два эти поведения позволяют искать пользователей по событиям и 
 *        события по пользователям.
 * 
 * @property CActiveRecord $owner
 * 
 * @see OmniRelationBehavior
 * @see http://www.yiiframework.com/doc/guide/1.1/en/database.ar#named-scopes
 * 
 * @todo автопроверки существования relation-связей при присоединении к классу owner-модели
 * @todo тесты для всех методов этого класса
 * @todo переименовать в CustomRelationTargetBehavior
 */
class OmniRelationTargetBehavior extends CustomScopesBehavior
{
    /**
     * @var string - поле в связанных записях содержащее класс owner-модели
     */
    public $defaultTypeField = 'objecttype';
    /**
     * @var string - поле в связанных записях содержащее id owner-модели
     */
    public $defaultIdField   = 'objectid';
    /**
     * @var string - Связь в owner-модели, которая используется по умолчанию для составления всех условий
     *               поиска дочерним записям (все методы этого класса производящие join-запрос используют
     *               этот параметр)  
     *               Содержит название HAS_MANY связи в owner-модели которая содержит все записи которые
     *               ссылаются сюда по objecttype/objectid
     */
    public $searchRelation;
    /**
     * @var array - связи по умолчанию: они должны быть у любого объекта
     * 
     * @todo пока не используется
     * @todo configItems - все настройки ссылающиеся сюда за значением
     * @todo comments    - все связанные комментарии
     */
    public $defaultRelations = array(
        // все элементы списка, содержащие эту модель
        'linkedItems' => array(
            'model'     => 'EasyListItem',
            'typeField' => 'objecttype',
            'idField'   => 'objectid',
        ),
        // все настройки, прикрепленные к этой модели
        'configParams' => array(
            'model'     => 'Config',
            'typeField' => 'objecttype',
            'idField'   => 'objectid',
        ),
    );
    /**
     * @var array - список всех дополнительных связей для модели, задается при подключении
     *              В этом массиве должны быть описаны все HAS_MANY связи с моделями, 
     *              которые ссылаются сюда по objecttype/objectid
     *              Пример:
     *              array(
     *                  'listItems' => array(
     *                      'model'     => 'EasyListItem',
     *                      'typeField' => 'objecttype',
     *                      'idField'   => 'objectid',
     *                  ),
     *                  ...
     *              )
     */
    public $customRelations = array();
    
    /**
     * @var array
     */
    protected $linkedModelsRelation = array();
    
    /**
     * @return void
     */
    /*public function init()
    {
        $this->owner->init();
    }*/
    
    /**
     * 
     * @param string $name
     * @return void
     */
    public function setSearchRelation($name)
    {
        $this->searchRelation = $name;
    }
    
    /**
     * 
     * @return string
     */
    public function getSearchRelation()
    {
        return $this->searchRelation;
    }
    
    /**
     * @see CActiveRecordBehavior::afterFind()
     */
    /*public function afterFind($event)
    {
        
    }*/
    
    /**
     * @return array
     *
     * @todo создавать исключение если связи с такими именами в модели уже есть
     */
    public function relations()
    {
        // получаем существующие связи модели и возвращаем список
        // в котором совмещены связи модели наши связи с дочерними объектами
        return CMap::mergeArray($this->owner->relations(), $this->createCustomRelations());
    }
    
    /**
     * Именованая группа условий: получить все записи к которым прикреплена переданная модель
     *
     * @param  CActiveRecord $model - модель связь с которой будем искать
     * @param  string $operator - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withLinkFromModel($model, $operator='AND')
    {
        if ( ! is_object($object) )
        {// передана модель целиком
            throw new CException('Не передана модель для составления условия');
        }
        // достаем из модели ее тип и id
        $objectType = get_class($object);
        $objectId   = $object->id;
    
        return $this->withLinkFrom($objectType, $objectId, $operator);
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
    public function withLinkFrom($objectType, $objectId=0, $operator='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->searchRelation => array(
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
            $this->searchRelation => array(
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
            $this->searchRelation => array(
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
            $this->searchRelation => array(
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
            $this->searchRelation => array(
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
                $this->searchRelation => array(
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
            $this->searchRelation => array(
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
     * Все записи, на которые ссылается хотя бы одна модель с указаным значением в указанном поле 
     *
     * @param  string    $field     - поле связанной записи в котором ищется значение
     * @param  int|array $values    - значение или список значений, которые нужно найти в поле
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withCustomLinkValue($field, $values, $operation='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->searchRelation => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'withCustomValue' => array($field, $values),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Все записи, кроме тех на которые ссылается хотя бы одна модель с указаным значением в указанном поле 
     *
     * @param  string    $field     - поле связанной записи в котором ищется значение
     * @param  int|array $values    - значение или список значений, которые нужно найти в поле
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptCustomLinkValue($field, $values, $operation='AND')
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array(
            $this->searchRelation => array(
                'joinType' => 'INNER JOIN',
                'scopes'   => array(
                    'exceptCustomValue' => array($field, $values),
                ),
            ),
        );
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * Создать все дополнительные связи для модели опираясь на значения по умолчанию
     *
     * @return array
     * 
     * @todo добавить проверку ошибок в структуре массива $this->customRelations
     * @todo обработать случай с нестандартными именами objecttype/objectid
     */
    protected function createCustomRelations()
    {
        // начинаем со связи по умолчанию
        $relations = array(
            'listItems' => array(
                CActiveRecord::HAS_MANY,
                'EasyListItem',
                'objectid',
                'scopes' => array(
                    'withObjectType' => array(get_class($this->owner)),
                ),
            ),
            'configParams' => array(
                CActiveRecord::HAS_MANY,
                'Config',
                'objectid',
                'scopes' => array(
                    'withObjectType' => array(get_class($this->owner)),
                ),
            ),
        );
        foreach ( $this->customRelations as $name => $data )
        {// затем добавляем все дополнительные
            $model     = '';
            $typeField = $this->defaultTypeField;
            $idField   = $this->defaultIdField;
            if ( ! is_array($data) )
            {
                $data = array('model' => $data);
            }
            if ( isset($data['model']) )
            {
                $model = $data['model'];
            }else
            {
                throw new CException('Не указан класс модели для создания связи');
            }
            if ( isset($data['typeField']) )
            {
                $typeField = $data['typeField'];
            }
            if ( isset($data['idField']) )
            {
                $idField = $data['idField'];
            }
            
            $relations[$name] = array(
                CActiveRecord::HAS_MANY,
                $model,
                $idField,
                'scopes' => array(
                    'withObjectType' => array(get_class($this->owner)),
                ),
            );
        }
        return $relations;
    }
}