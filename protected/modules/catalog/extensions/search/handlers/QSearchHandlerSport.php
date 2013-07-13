<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Виды спорта"
 */
class QSearchHandlerSport extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = implode("', '", $data['sporttype']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('sporttypes');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('issportsman', 1);
        $criteria->addCondition("`sporttypes`.`value` IN ('".$values."')");
    
        return $criteria;
    }
}