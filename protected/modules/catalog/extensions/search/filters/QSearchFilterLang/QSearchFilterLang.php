<?php

/**
 * Фильтр поиска по полю "Иностранный язык"
 */
class QSearchFilterLang extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('language');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Иностранный язык";
    }
}