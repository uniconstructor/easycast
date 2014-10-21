<?php

/**
 * Behavior for CActiveRecord model to store order of records.
 * When you create new model and save it, it appears at the end of list.
 * You can manually change the position of record with {@see moveUp} and {@see moveDown} methods.
 * When you delete model, all subsequent models are moved up one position.
 *
 * You must manually add field for position in table.
 * ALTER TABLE `v_forums`
 * ADD `order` tinyint unsigned NOT NULL,
 * ADD UNIQUE `order` (`order`);
 *
 * @author wapmorgan (wapmorgan@gmail.com)
 *        
 */
class SortableModel extends CActiveRecordBehavior
{
    /**
     * Field that stores 1-based order
     */
    public $orderField = 'order';
    public $condition;
    public $params = array();

    /**
     * BeforeSave event handler.
     * Sets orderField.
     */
    public function beforeSave($event)
    {
        if ( $this->owner->isNewRecord )
        {
            $number = ($data = $this->createCommand()
                ->select(new CDbExpression('MAX(`' . $this->orderField . '`)'))
                ->andWhere($this->condition, $this->params)
                ->queryScalar()) > 0 ? $data + 1 : 1;
            $this->owner->setAttribute($this->orderField, $number);
        }
    }

    /**
     * Sets default order
     */
    public function beforeFind($event)
    {
        $this->owner->dbCriteria->order = '`' . $this->orderField . '`';
    }

    /**
     * AfterDelete event handler.
     * Updates order.
     */
    public function afterDelete($event)
    {
        $criteria = new CDbCriteria(array(
            'condition' => '`' . $this->orderField . '` > :position' . ($this->condition ? ' AND ' . $this->condition : ''), 
            'params' => array_merge(array(
                ':position' => $this->owner->attributes[$this->orderField]), $this->params), 
            'order' => '`' . $this->orderField . '`'));
        $this->owner->dbConnection->commandBuilder->createUpdateCommand($this->owner->tableName(), array(
            $this->orderField => new CDbExpression('`' . $this->orderField . '` - 1')), $criteria)->execute();
    }

    /**
     * Moves record closer to the top (decreases order field).
     */
    public function moveUp()
    {
        if ( $this->owner->attributes[$this->orderField] > 1 )
        {
            $this->ensureNotLocked();
            $prev = $this->owner->previous()->find();
            if ( ($externalTransaction = $this->owner->dbConnection->currentTransaction) === null ) $transaction = $this->owner->dbConnection->beginTransaction();
            $position = $this->owner->attributes[$this->orderField];
            $this->owner->setAttribute($this->orderField, 0);
            $this->owner->save(false, array(
                $this->orderField));
            $prev->setAttribute($this->orderField, $position);
            $prev->save(false, array(
                $this->orderField));
            $this->owner->setAttribute($this->orderField, $position - 1);
            $this->owner->save(false, array(
                $this->orderField));
            if ( $externalTransaction === null ) $transaction->commit();
            return true;
        }
    }

    /**
     * Moves record closer to the bottom (increases order field).
     */
    public function moveDown()
    {
        $count = $this->owner->count();
        if ( $this->owner->attributes[$this->orderField] < $count )
        {
            $this->ensureNotLocked();
            $next = $this->owner->next()->find();
            if ( ($externalTransaction = $this->owner->dbConnection->currentTransaction) === null ) $transaction = $this->owner->dbConnection->beginTransaction();
            $position = $this->owner->attributes[$this->orderField];
            $this->owner->setAttribute($this->orderField, 0);
            $this->owner->save();
            $next->setAttribute($this->orderField, $position);
            $next->save();
            $this->owner->setAttribute($this->orderField, $position + 1);
            $this->owner->save();
            if ( $externalTransaction === null ) $transaction->commit();
            return true;
        }
    }
    
    /**
     * Вставить новую запись после указанной, пересчитав нумерацию
     *
     * @param  CActiveRecord $newModel
     * @param  int $id
     * @return bool
     */
    public function insertAfter($newModel, $id)
    {
         
    }

    /**
     * Creates DB command
     */
    private function createCommand()
    {
        return $this->owner->dbConnection->createCommand()->from($this->owner->tableName());
    }

    /**
     * Named scope.
     * Selectes next record.
     */
    public function next()
    {
        $this->owner->dbCriteria->mergeWith(array(
            'condition' => '`' . $this->orderField . '` = :position' . ($this->condition ? ' AND ' . $this->condition : ''), 
            'params' => array_merge(array(
                ':position' => $this->owner->attributes[$this->orderField] + 1), $this->params)));
        return $this->owner;
    }

    /**
     * Named scope.
     * Selectes previous record.
     */
    public function previous()
    {
        $this->owner->dbCriteria->mergeWith(array(
            'condition' => '`' . $this->orderField . '` = :position' . ($this->condition ? ' AND ' . $this->condition : ''), 
            'params' => array_merge(array(
                ':position' => $this->owner->attributes[$this->orderField] - 1), $this->params)));
        return $this->owner;
    }

    /**
     * Ensures table is not locked
     */
    private function ensureNotLocked()
    {
        if ( $this->owner->findByAttributes(array(
            $this->orderField => 0)) !== null ) throw new Exception('Table order is locked!');
    }
}