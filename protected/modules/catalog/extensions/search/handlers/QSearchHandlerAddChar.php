<?php

/**
 * Обработчик поиска по доп. характеристикам
 */
class QSearchHandlerAddChar extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data   = $this->getFilterData();
        $values = implode("', '", $data['addchar']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('addchars');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
    
        $criteria->addCondition("`addchars`.`value` IN ('{$values}')");
        return $criteria;
    }
}