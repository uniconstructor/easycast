<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "тип внешности"
 */
class QSearchHandlerStrip extends QSearchHandlerBase
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
        
        $criteria = new CDbCriteria();
        if ( isset($data['striptype']) AND is_array($data['striptype']) )
        {
            $criteria->addInCondition('striptype', $data['striptype']);
        }
        if ( isset($data['striplevel'])  AND is_array($data['striplevel']) )
        {
            $criteria->addInCondition('striplevel', $data['striplevel']);
        }
        
        return $criteria;
    }
}