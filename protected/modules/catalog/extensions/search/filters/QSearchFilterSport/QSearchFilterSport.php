<?php

/**
 * Фильтр поиска по полю "Виды спорта"
 */
class QSearchFilterSport extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('sporttype');
    
    /**
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Виды спорта";
    }
}