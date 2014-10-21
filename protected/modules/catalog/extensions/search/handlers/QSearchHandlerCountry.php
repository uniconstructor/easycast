<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтров "Страна рождения", "Гражданство", и "Страна проживания"
 */
class QSearchHandlerCountry extends QSearchHandlerBase
{
    /**
     * @var string - название поля анкеты в котором мы будем искать страну
     */
    protected $countryField;
    
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data     = $this->getFilterData();
        $criteria = new CDbCriteria();
        
        if ( $this->countryField AND isset($data[$this->countryField]) AND $data[$this->countryField] )
        {
            $criteria->addInCondition($this->countryField, $data[$this->countryField]);
        }
        return $criteria;
    }
    
    /**
     * @see QSearchHandlerBase::getFilterData()
     */
    protected function getFilterData()
    {
        // Получаем имя элемента в массиве, в котором должны находится данные из фильтра поиска
        $name = QSearchFilterBase::defaultPrefix().'nativecountryid';
        if ( isset($this->data[$name]) AND ! empty($this->data[$name]) )
        {// Данные фильтра есть в массиве - значит он используется
            $this->countryField = 'nativecountryid';
            return $this->data[$name];
        }
        $name = QSearchFilterBase::defaultPrefix().'countryid';
        if ( isset($this->data[$name]) AND ! empty($this->data[$name]) )
        {// Данные фильтра есть в массиве - значит он используется
            $this->countryField = 'countryid';
            return $this->data[$name];
        }
        $name = QSearchFilterBase::defaultPrefix().'currentcountryid';
        if ( isset($this->data[$name]) AND ! empty($this->data[$name]) )
        {// Данные фильтра есть в массиве - значит он используется
            $this->countryField = 'currentcountryid';
            return $this->data[$name];
        }
        return null;
    }
}