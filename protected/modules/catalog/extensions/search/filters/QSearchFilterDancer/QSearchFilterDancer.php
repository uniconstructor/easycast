<?php

/**
 * Фильтр поиска по полю "Танцор"
 */
class QSearchFilterDancer extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('dancetype');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Танцор";
    }
}