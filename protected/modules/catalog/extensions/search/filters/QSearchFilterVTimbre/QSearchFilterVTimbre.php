<?php

/**
 * Фильтр поиска по полю "Тембр голоса"
 */
class QSearchFilterVTimbre extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('voicetimbre');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Тембр голоса";
    }
}