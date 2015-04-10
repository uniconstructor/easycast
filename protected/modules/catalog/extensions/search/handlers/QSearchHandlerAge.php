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
        $data     = $this->getFilterData();
        $criteria = new CDbCriteria();
        $alias    = Questionary::model()->getTableAlias(true);
        
        $minTimestamp = null;
        $maxTimestamp = null;
        
        if ( isset($data['minage']) AND $minAge = intval($data['minage']) )
        {// минимальный возраст
            $minTimestamp = time() - ( $minAge * 365 * 24 * 3600 );
        }
        if ( isset($data['maxage']) AND $maxAge = intval($data['maxage']) )
        {// максимальный возраст: прибавляем единицу к указанному в форме,
            // чтобы найти всех участников до указанного возраста включительно
            $maxTimestamp = time() - ( (1 + $maxAge) * 365 * 24 * 3600 );
        }
        // условие
        if ( $minTimestamp AND ! $maxTimestamp )
        {
            $criteria->compare($alias.'.`birthdate`', '<='.$minTimestamp);
        }elseif ( $maxTimestamp AND ! $minTimestamp )
        {
            $criteria->compare($alias.'.`birthdate`', '>='.$maxTimestamp);
        }elseif ( $minTimestamp AND $maxTimestamp )
        {
            $criteria->addBetweenCondition($alias.'.`birthdate`', $minTimestamp, $maxTimestamp);
        }
        return $criteria;
    }
}