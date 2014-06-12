<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "тип внешности"
 */
class QSearchHandlerLookType extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        if ( $values = $data['looktype'] )
        {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('looktype', $values);
            // включаем в выборку тех у кого поле не заполнено
            $criteria->addInCondition('looktype', array(0 => null), 'OR');
        }
        return $criteria;
    }
}