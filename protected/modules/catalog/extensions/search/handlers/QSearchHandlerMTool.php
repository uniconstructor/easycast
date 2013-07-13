<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Музыкальный инструмент"
 * (Название класса MTool выбрано для краткости - длина имени класса не может быть больше 24 символов)
 */
class QSearchHandlerMTool extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = implode("', '", $data['instrument']);
    
        $criteria = new CDbCriteria();
        $criteria->with = array('instruments');
        // Необходимо для корректного выполнения реляционного запроса
        $criteria->together = true;
        
        $criteria->compare('ismusician', 1);
        $criteria->addCondition("instruments.value IN ('".$values."')");
    
        return $criteria;
    }
}