<?php

/**
 * Класс сборки поискового запроса для поля "игровой возраст"
 */
class QSearchHandlerPlayAge extends QSearchHandlerBase
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
        if ( isset($data['minplayage']) AND $data['minplayage'] )
        {
            $condition = '(`playagemin` >= :minplayage ) OR ( `playagemin` < :minplayage AND `playagemax` >= :minplayage)';
            $criteria->addCondition($condition);
            $criteria->params[':minplayage'] = $data['minplayage'];
        }
        if ( isset($data['maxplayage']) )
        {
            $condition = '(`playagemax` <= :maxplayage AND `playagemax` IS NOT NULL AND `playagemax` != 0 ) OR 
                ( `playagemax` > :maxplayage AND `playagemin` <= :maxplayage AND `playagemin` IS NOT NULL AND `playagemin` != 0 )';
            $criteria->addCondition($condition);
            $criteria->params[':maxplayage'] = $data['maxplayage'];
        }
        return $criteria;
    }
}