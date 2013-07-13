<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "татуировки"
 */
class QSearchHandlerTatoo extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $tatoo = $data['tatoo'];
        
        $criteria = new CDbCriteria();
        $criteria->compare('hastatoo', $tatoo);
        
        return $criteria;
    }
}