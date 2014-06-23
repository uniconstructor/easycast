<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Город"
 */
class QSearchHandlerCity extends QSearchHandlerBase
{
    /**
     * @var string - название поля анкеты в котором мы будем искать город
     */
    protected $cityField = 'cityid';

    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data     = $this->getFilterData();
        $criteria = new CDbCriteria();
        
        if ( $this->cityField AND isset($data[$this->cityField]) AND $data[$this->cityField] )
        {
            $criteria->addInCondition($this->cityField, $data[$this->cityField]);
        }
        return $criteria;
    }

    /**
     * @see QSearchHandlerBase::getFilterData()
     */
    protected function getFilterData()
    {
        // Получаем имя элемента в массиве, в котором должны находится данные из фильтра поиска
        $name = QSearchFilterBase::defaultPrefix().'city';
        if ( isset($this->data[$name]) AND ! empty($this->data[$name]) )
        {// Данные фильтра есть в массиве - значит он используется
            $this->cityField = 'cityid';
            return $this->data[$name];
        }
        return null;
    }
}