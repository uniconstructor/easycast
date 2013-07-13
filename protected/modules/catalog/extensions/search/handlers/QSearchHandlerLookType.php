<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "тип внешности"
 */
class QSearchHandlerLookType extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = $data['looktype'];
    
        $criteria = new CDbCriteria();
        $criteria->addInCondition('looktype', $values);
    
        return $criteria;
    }
}