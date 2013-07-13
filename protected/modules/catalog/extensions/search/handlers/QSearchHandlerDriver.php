<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Водительские права"
 */
class QSearchHandlerDriver extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = $data['driver'];
    
        $criteria = new CDbCriteria();
        $criteria->with = array('skills');
        // together Необходимо для корректного выполнения реляционного запроса
        // ( http://www.yiiframework.com/wiki/280/1-n-relations-sometimes-require-cdbcriteria-together/ )
        $criteria->together = true;
        
        $criteria->compare('hasskills', 1);
        $criteria->addInCondition("`skills`.`value`", $values);
    
        return $criteria;
    }
}