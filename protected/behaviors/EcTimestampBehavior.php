<?php

// подключаем родительский класс 
Yii::import('zii.behaviors.CTimestampBehavior');

/**
 * Заготовка phpdoc чтобы нормально работал code assist по методам owner-класса при обращении к модели
 * Скопируйте ее в комментарий к AR-классу модели к которому прикрепляется это поведение
 * 
 * Методы класса EcTimestampBehavior:
 * @method CActiveRecord createdBefore(int $time, string $operation='AND')
 * @method CActiveRecord createdAfter(int $time, string $operation='AND')
 * @method CActiveRecord updatedBefore(int $time, string $operation='AND')
 * @method CActiveRecord updatedAfter(int $time, string $operation='AND')
 * @method CActiveRecord modifiedOnly()
 * @method CActiveRecord neverModified()
 * @method CActiveRecord lastCreated()
 * @method CActiveRecord firstCreated()
 * @method CActiveRecord lastModified()
 * @method CActiveRecord firstModified()
 */

/**
 * Расширенный класс для работы с датами создания/изменения объекта
 * 
 * @todo документировать все методы
 * @todo дополнительные именованые группы условий: 
 *       - созданные за день/неделю/месяц, 
 *       - отредактированые за тот же период
 * @todo переписать все подключения scopes: вызывать список именованых групп условий поиска
 *       из класса, к которому подключается поведение, а не из самого поведения
 *       Проблема вот в чем: если вызвать функцию lastModified() при обычном извлечении записей
 *       то все работает, но если попытаться использовать то же условие при создании CActiveDataProvider
 *       то они не будут найдены: видимо при поиске записей через CActiveDataProvider к классу модели
 *       не подключаются поведения
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
    public function timestampScopes()
    {
        $modelScopes     = $this->owner->scopes();
        $timestampScopes = $this->getTimestampScopes();
        
        return CMap::mergeArray($timestampScopes, $modelScopes);
    }
    
    /**
     * Список стандартных приглашений 
     * 
     * @return array
     */
    public function getDefaultTimestampScopes()
    {
        return array(
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
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    /*public function attributeLabels()
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
    }*/
    
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