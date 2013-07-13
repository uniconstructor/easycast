<?php

/**
 * Класс виджета поиска по полю "цвет глаз"
 */
class QSearchFilterEyeColor extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('eyecolor');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Цвет глаз";
    }
}