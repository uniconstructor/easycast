<?php

/**
 * Поведение, позволяющее задать условие поиска по любому полю в scope-формате
 * Это часто требуется при составления сложного запроса по нескольким таблицам: при таких
 * запросах часто неудобно указывать условия поиска по списку id строкой или каждый раз 
 * создавать новый объект CDbCriteria для параметра condition
 * 
 * Подключение этого поведения к модели дает вам возможность все условия поиска выборки (CDbCriteria) 
 * одном массиве scopes, а также позволяет одной цепочкой вызовов решить большинство задач
 * по выборке записей
 * 
 * @see CDbCriteria::$with
 * @see CDbCriteria::$together
 * @see http://www.yiiframework.com/doc/guide/1.1/en/database.ar#named-scopes
 */
class CustomScopesBehavior extends CActiveRecordBehavior
{
    /**
     * Именованая группа условий: все записи с указанными id
     *
     * @param  int|array $id - id записи или массив из id записей
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withId($id, $operation='AND')
    {
        return $this->withCustomValue('id', $id, $operation);
    }
    
    /**
     * Условие поиска: все модели кроме тех которые содержат хотя бы одно
     * из перечисленных значений внутри указанного поля
     *
     * @param  int|array $id - id записи или массив из id записей
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function exceptId($id, $operation='AND')
    {
        return $this->exceptCustomValue('id', $id, $operation);
    }
    
    /**
     * Все записи с указаным значением в указанном поле
     *
     * @param  string    $field     - поле в котором ищется значение
     * @param  int|array $values    - значение или список значений, которые нужно найти в поле
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    public function withCustomValue($field, $values, $operation='AND')
    {
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
        if ( ! is_array($values) )
        {
            $values = array($values);
        }
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition($this->owner->getTableAlias(true).".`{$field}`", $values);
    
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
    
        return $this->owner;
    }
    
    /**
     * Установить поле модели, которое будет использовано в качестве ключа массива в итоговой выборке
     * 
     * @param  string $field
     * @return CActiveRecord
     */
    public function setResultIndex($field)
    {
        $criteria = new CDbCriteria();
        $criteria->index = $field;
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
            
        return $this->owner;
    }
}