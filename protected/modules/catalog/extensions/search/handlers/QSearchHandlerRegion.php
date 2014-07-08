<?php

/**
 * Обработчик фильтра поиска по региону
 */
class QSearchHandlerRegion extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $criteria = new CDbCriteria();
        $data     = $this->getFilterData();
        
        if ( ! isset($data['regionid']) OR ! is_array($data['regionid']) )
        {
            return;
        }
        $criteria->scopes = array(
            'fromRegions' => array($data['regionid']),
        );
        return $criteria;
    }
}