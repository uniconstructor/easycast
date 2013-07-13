<?php

/**
 * Класс виджета поиска по полю "размер груди"
 */
class QSearchFilterTitSize extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('titsize');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Размер груди";
    }
}