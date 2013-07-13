<?php

/**
 * Класс сборки поискового запроса для поля "Рост"
 */
class QSearchHandlerHeight extends QSearchHandlerBase
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
        if ( isset($data['minheight']) )
        {
            $criteria->addCondition('`height` >= :minheight');
            $criteria->params[':minheight'] = $data['minheight'];
        }
        if ( isset($data['maxheight']) )
        {
            $criteria->addCondition('`height` <= :maxheight AND `height` IS NOT NULL');
            $criteria->params[':maxheight'] = $data['maxheight'];
        }
        
        return $criteria;
    }
}