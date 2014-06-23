<?php

/**
 * Фильтр поиска по полю "Экстремальный спорт"
 */
class QSearchFilterExtreme extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('extremaltype');
    
    /**
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Экстремальный спорт";
    }
}