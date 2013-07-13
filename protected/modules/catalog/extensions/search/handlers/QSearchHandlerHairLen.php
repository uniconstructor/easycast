<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "длина волос"
 * Название этого класса пришлось сократить, т. к. в Yii нельзя создавать классы с именем длиннее 24 символов 
 */
class QSearchHandlerHairLen extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data = $this->getFilterData();
        $values = $data['hairlength'];
        
        $criteria = new CDbCriteria();
        $criteria->addInCondition('hairlength', $values);
    
        return $criteria;
    }
}