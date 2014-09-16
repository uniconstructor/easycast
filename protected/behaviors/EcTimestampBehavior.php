<?php

/**
 * Расширенный класс для работы с датами создания/изменения объекта
 */
class EcTimestampBehavior extends CTimestampBehavior
{
    /**
     * @var string
     */
    public $createAttribute = 'timecreated';
    /**
     * @var string
     */
    public $updateAttribute = 'timemodified';
    
    /**
     * @see CActiveRecord::scopes()
     */
    public function scopes()
    {
        $modelScopes     = $this->owner->scopes();
        $timestampScopes = array(
            // отредактированые записи
            'modified' => array(
                'condition' => $this->owner->getTableAlias(true).".`{$this->createAttribute}` > 0",
            ),
            // никогда не редактировавшиеся записи
            'neverModified' => array(
                'condition' => $this->owner->getTableAlias(true).".`{$this->updateAttribute}` = 0",
            ),
	    );
        return CMap::mergeArray($timestampScopes, $modelScopes);
    }
    
    /**
     * 
     * @param int $time
     * @param string $operation
     * @return CActiveRecord
     */
    public function createdBefore($time, $operation='AND')
    {
        if ( ! $this->createAttribute )
        {
            throw new CException('В модели нет нужного поля');
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).".`{$this->createAttribute}`", '<='.$time);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     * 
     * @param int $time
     * @param string $operation
     * @return CActiveRecord
     */
    public function createdAfter($time, $operation='AND')
    {
        if ( ! $this->createAttribute )
        {
            throw new CException('В модели нет нужного поля');
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).".`{$this->createAttribute}`", '>='.$time);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     *
     * @param int $time
     * @param string $operation
     * @return CActiveRecord
     */
    public function updatedBefore($time, $operation='AND')
    {
        if ( ! $this->updateAttribute )
        {
            throw new CException('В модели нет нужного поля');
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).".`{$this->updateAttribute}`", '<='.$time);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
    
    /**
     *
     * @param int $time
     * @param string $operation
     * @return CActiveRecord
     */
    public function updatedAfter($time, $operation='AND')
    {
        if ( ! $this->updateAttribute )
        {
            throw new CException('В модели нет нужного поля');
        }
        $criteria = new CDbCriteria();
        $criteria->compare($this->owner->getTableAlias(true).".`{$this->updateAttribute}`", '>='.$time);
        
        $this->owner->getDbCriteria()->mergeWith($criteria, $operation);
        
        return $this->owner;
    }
}