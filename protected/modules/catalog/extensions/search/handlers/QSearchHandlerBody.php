<?php

/**
 * Класс сборки поискового запроса для поля "Возраст"
 */
class QSearchHandlerBody extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $criteria = new CDbCriteria();
        
        // для каждого параметра тела алгоритм составления запроса одинаковый
        $fields = array('chestsize', 'waistsize', 'hipsize');
        
        foreach ( $fields as $field )
        {// перебираем 3 поля формы: обхват груди, талия, бедра и для каждого параметра
            // составляем критерий поиска (максимальное и минимальное ограничение)
            $minField = 'min'.$field;
            $maxField = 'max'.$field;
            if ( isset($data[$minField]) )
            {
                $criteria->addCondition("`{$field}` >= :{$minField}");
                $criteria->params[':'.$minField] = $data[$minField];
            }
            if ( isset($data[$maxField]) )
            {
                $criteria->addCondition("`{$field}` <= :{$maxField} AND `{$field}` IS NOT NULL");
                $criteria->params[':'.$maxField] = $data[$maxField];
            }
        }
        
        return $criteria;
    }
}