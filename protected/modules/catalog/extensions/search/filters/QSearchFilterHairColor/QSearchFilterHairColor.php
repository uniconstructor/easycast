<?php

/**
 * Класс виджета поиска по полю "цвет волос"
 */
class QSearchFilterHairColor extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('haircolor');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Цвет волос";
    }
}