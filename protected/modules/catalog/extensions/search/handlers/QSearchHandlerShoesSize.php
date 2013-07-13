<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Телосложение"
 */
class QSearchHandlerShoesSize extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = $data['shoessize'];
    
        $criteria = new CDbCriteria();
        $criteria->addInCondition('shoessize', $values);
    
        return $criteria;
    }
}