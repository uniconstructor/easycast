<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Экстремальный спорт"
 */
class QSearchHandlerExtreme extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = implode("', '", $data['extremaltype']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('extremaltypes');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('isextremal', 1);
        $criteria->addCondition("`extremaltypes`.`value` IN ('".$values."')");
    
        return $criteria;
    }
}