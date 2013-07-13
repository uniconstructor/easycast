<?php

/**
 * Класс сборки поискового запроса для поля "Вес"
 */
class QSearchHandlerWeight extends QSearchHandlerBase
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
        if ( isset($data['minweight']) )
        {
            $criteria->addCondition('`weight` >= :minweight');
            $criteria->params[':minweight'] = $data['minweight'];
        }
        if ( isset($data['maxweight']) )
        {
            $criteria->addCondition('`weight` <= :maxweight AND `weight` IS NOT NULL');
            $criteria->params[':maxweight'] = $data['maxweight'];
        }
        
        return $criteria;
    }
}