<?php

/**
 * Фильтр поиска по полю "Работа в театре"
 */
class QSearchFilterTheatre extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('theatres');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Работа в театре";
    }
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBaseSelect2::getMenuVariants()
     */
    protected function getMenuVariants()
    {
        return QTheatreInstance::model()->getTheatreList();
    }
}