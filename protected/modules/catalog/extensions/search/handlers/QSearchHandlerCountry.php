<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Страна рождения"
 */
class QSearchHandlerCountry extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data   = $this->getFilterData();
        $values = $data['nativecountryid'];
    
        $criteria = new CDbCriteria();
        $criteria->addInCondition('nativecountryid', $values);
    
        return $criteria;
    }
}