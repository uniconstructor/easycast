<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Телосложение"
 */
class QSearchHandlerPType extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = $data['physiquetype'];
    
        $criteria = new CDbCriteria();
        $criteria->addInCondition('physiquetype', $values);
    
        return $criteria;
    }
}