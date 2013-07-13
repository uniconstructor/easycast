<?php

/**
 * Класс виджета поиска по полю "Размер одежды"
 */
class QSearchFilterWearSize extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('wearsize');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Размер одежды";
    }
}