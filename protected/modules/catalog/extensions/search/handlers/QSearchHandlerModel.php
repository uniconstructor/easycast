<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Специализация ведущего"
 */
class QSearchHandlerModel extends QSearchHandlerBase
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
        
        if ( isset($data['modeltype']) AND is_array($data['modeltype']) )
        {
            $conditions = array();
            // определяем, какие модели нам нужны
            if ( in_array('model', $data['modeltype']) )
            {
                $conditions[] = '`t`.`ismodel` = 1';
            }
            if ( in_array('photomodel', $data['modeltype']) )
            {
                $conditions[] = '`t`.`isphotomodel` = 1';
            }
            if ( in_array('promomodel', $data['modeltype']) )
            {
                $conditions[] = '`t`.`ispromomodel` = 1';
            }
            
            if ( ! empty($conditions) )
            {// ищем любой нужный вариант
                $condition = '('.implode(' OR ', $conditions).')';
                $criteria->addCondition($condition.'');
            }
        }
        
        return $criteria;
    }
}