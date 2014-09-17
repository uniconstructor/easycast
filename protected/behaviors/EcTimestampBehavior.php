<?php

/**
 * Расширенный класс для работы с датами создания/изменения объекта
 * 
 * @todo документировать все методы
 * @todo дополнительные именованые группы условий: 
 *       - созданные за день/неделю/месяц, 
 *       - отредактированые за тот же период
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
            'modifiedOnly' => array(
                'condition' => $this->owner->getTableAlias(true).".`{$this->updateAttribute}` > 0",
            ),
            // никогда не редактировавшиеся записи
            'neverModified' => array(
                'condition' => $this->owner->getTableAlias(true).".`{$this->updateAttribute}` = 0",
            ),
            // порядок сортировки: по времени создания (сначала новые)
            'lastCreated' => array(
                'order' => $this->owner->getTableAlias(true).".`{$this->createAttribute}` DESC",
            ),
            // порядок сортировки: по времени создания (сначала старые)
            'firstCreated' => array(
                'order' => $this->owner->getTableAlias(true).".`{$this->createAttribute}` ASC",
            ),
            // порядок сортировки: по времени изменения (сначала новые)
            'lastModified' => array(
                'order' => $this->owner->getTableAlias(true).".`{$this->updateAttribute}` DESC",
            ),
            // порядок сортировки: по времени изменения (сначала старые)
            'firstModified' => array(
                'order' => $this->owner->getTableAlias(true).".`{$this->updateAttribute}` ASC",
            ),
	    );
        return CMap::mergeArray($timestampScopes, $modelScopes);
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = $this->owner->attributeLabels();
        if ( $this->createAttribute AND ( ! isset($labels[$this->createAttribute]) OR $labels[$this->createAttribute] === 'Timecreated' ) )
        {
            $labels[$this->createAttribute] = Yii::t('coreMessages', 'timecreated');
        }
        if ( $this->updateAttribute AND ( ! isset($labels[$this->updateAttribute]) OR $labels[$this->updateAttribute] === 'Timemodified' ) )
        {
            $labels[$this->updateAttribute] = Yii::t('coreMessages', 'timemodified');
        }
        return $labels;
    }
    
    /**
     * 
     * @param  int    $time
     * @param  string $operation
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
     * @param  int    $time
     * @param  string $operation
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
     * @param  int    $time
     * @param  string $operation
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
     * @param  int    $time
     * @param  string $operation
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