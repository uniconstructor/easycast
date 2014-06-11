<?php

/**
 * Класс виджета поиска по полю "Размер обуви"
 */
class QSearchFilterShoesSize extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('shoessize');
    
    /**
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Размер обуви";
    }
}