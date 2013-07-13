<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Специализация ведущего"
 */
class QSearchHandlerMCType extends QSearchHandlerBase
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
        if ( isset($data['mctype']) AND is_array($data['mctype']) )
        {
            if ( in_array('emcee', $data['mctype']) AND in_array('tvshowmen', $data['mctype']) )
            {// нужны все ведущие
                $criteria->addCondition("(`t`.`isemcee` = 1 OR `t`.`istvshowmen` = 1)");
            }elseif ( in_array('emcee', $data['mctype']) )
            {// нужны только ведущие мероприятий
                $criteria->compare('isemcee', 1);
            }elseif ( in_array('tvshowmen', $data['mctype']) )
            {// нужны только телеведущие
                $criteria->compare('istvshowmen', 1);
            }
        }
        
        return $criteria;
    }
}