<?php

/**
 * Фильтр поиска по полю "Тип вокала"
 */
class QSearchFilterVType extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('vocaltype');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Тип вокала";
    }
}