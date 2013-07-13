<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Тембр голоса"
 */
class QSearchHandlerVTimbre extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = implode("', '", $data['voicetimbre']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('voicetimbres');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('issinger', 1);
        $criteria->addCondition("voicetimbres.value IN ('".$values."')");
    
        return $criteria;
    }
}