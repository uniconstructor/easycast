<?php

/**
 * Класс сборки поискового запроса для поля "игровой возраст"
 * 
 * @deprecated
 */
class QSearchHandlerPlayAge extends QSearchHandlerBase
{
    /**
     * @see QSearchHandlerBase::enabled()
     */
    public function enabled()
    {
        return false;
    }
    
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        //$data = $this->getFilterData();
        //$criteria = new CDbCriteria();
        //return $criteria;
        
        return null;
    }
}