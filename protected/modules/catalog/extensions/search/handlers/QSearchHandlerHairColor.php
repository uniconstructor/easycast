<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "цвет волос"
 */
class QSearchHandlerHairColor extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = $data['haircolor'];
    
        $criteria = new CDbCriteria();
        $criteria->addInCondition('haircolor', $values);
    
        return $criteria;
    }
}