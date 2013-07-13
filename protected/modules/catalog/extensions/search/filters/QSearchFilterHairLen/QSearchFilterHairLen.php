<?php

/**
 * Класс виджета поиска по полю "длина волос"
 */
class QSearchFilterHairLen extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('hairlength');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Длина волос";
    }
}