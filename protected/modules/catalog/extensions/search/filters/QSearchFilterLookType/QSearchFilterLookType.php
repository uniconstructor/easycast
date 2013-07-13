<?php

/**
 * Класс виджета поиска по полю "тип внешности"
 */
class QSearchFilterLookType extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('looktype');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Тип внешности";
    }
}