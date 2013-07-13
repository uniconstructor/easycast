<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Тип вокала"
 */
class QSearchHandlerVType extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = implode("', '", $data['vocaltype']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('vocaltypes');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('issinger', 1);
        $criteria->addCondition("vocaltypes.value IN ('".$values."')");
    
        return $criteria;
    }
}