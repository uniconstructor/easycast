<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Танцор"
 */
class QSearchHandlerDancer extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data   = $this->getFilterData();
        $values = implode("', '", $data['dancetype']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('dancetypes');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('isdancer', 1);
        $criteria->addCondition("`dancetypes`.`value` IN ('".$values."')");
    
        return $criteria;
    }
}