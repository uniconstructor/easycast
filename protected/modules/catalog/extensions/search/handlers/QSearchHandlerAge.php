<?php

/**
 * Класс сборки поискового запроса для поля "Возраст"
 */
class QSearchHandlerAge extends QSearchHandlerBase
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
        if ( isset($data['minage']) AND $data['minage'] )
        {
            $criteria->addCondition('`birthdate` <= ( CAST(UNIX_TIMESTAMP() AS SIGNED) - CAST(:minage*366*24*3600 AS SIGNED) )');
            $criteria->params[':minage'] = $data['minage'];
        }
        if ( isset($data['maxage']) )
        {
            $criteria->addCondition('`birthdate` >= ( CAST(UNIX_TIMESTAMP() AS SIGNED) - CAST(:maxage*366*24*3600 AS SIGNED) )');
            $criteria->params[':maxage'] = $data['maxage'];
        }
        return $criteria;
    }
}