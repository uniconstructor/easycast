<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "пол"
 */
class QSearchHandlerGender extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data   = $this->getFilterData();
        $gender = $data['gender'];
        
        $criteria = new CDbCriteria();
        $criteria->compare('gender', $gender);
        
        return $criteria;
    }
}