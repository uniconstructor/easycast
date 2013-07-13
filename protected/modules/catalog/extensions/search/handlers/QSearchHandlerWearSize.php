<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "размер одежды"
 */
class QSearchHandlerWearSize extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = $data['wearsize'];
    
        $criteria = new CDbCriteria();
        $criteria->addInCondition('wearsize', $values);
    
        return $criteria;
    }
}