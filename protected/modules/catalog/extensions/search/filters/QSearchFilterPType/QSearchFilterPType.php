<?php

/**
 * Класс виджета поиска по полю "телосложение"
 */
class QSearchFilterPType extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('physiquetype');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Телосложение";
    }
}