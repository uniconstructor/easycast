<?php

/**
 * Обработчик фильтра поиска по статусу
 */
class QSearchHandlerStatus extends QSearchHandlerBase
{
    /**
     * Получить CDbCriteria при поиске
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data   = $this->getFilterData();
        $values = implode("', '", CHtmlPurifier::purify($data['status']));
    
        $criteria = new CDbCriteria();
        $criteria->addCondition("`t`.`status` IN ('{$values}')");
    
        return $criteria;
    }
}