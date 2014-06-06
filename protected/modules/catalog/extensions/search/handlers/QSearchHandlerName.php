<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "имя"
 */
class QSearchHandlerName extends QSearchHandlerBase
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $criteria = new CDbCriteria();
        $data = $this->getFilterData();
        $name = trim($data['name']);
        
        if ( mb_ereg(' ', $name) )
        {// Если в поле введены и имя и фамилия -
            // то разбиваем строку по пробелам для того чтобы отделить имя от фамилии при дальнейшем поиске
            $parts = explode(' ', $name);
            // Поскольку мы не знаем, что введено сначала: имя или фамилия - то будем искать по всем полям
            $criteria->addInCondition('firstname', $parts);
            $criteria->addInCondition('lastname', $parts, 'OR');
        }else
        {// в поле введено одно слово
            // Поскольку мы не знаем, что введено сначала: имя или фамилия - то будем искать по всем полям
            $criteria->compare('firstname', $name);
            $criteria->compare('lastname', $name, false, 'OR');
        }
        return $criteria;
    }
}