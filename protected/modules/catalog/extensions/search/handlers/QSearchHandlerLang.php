<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Иностранный язык"
 */
class QSearchHandlerLang extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = implode("', '", $data['language']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('languages');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('haslanuages', 1);
        $criteria->addCondition("`languages`.`value` IN ('".$values."')");
    
        return $criteria;
    }
}