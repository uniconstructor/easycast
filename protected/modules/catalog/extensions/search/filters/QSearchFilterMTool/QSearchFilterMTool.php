<?php

/**
 * Фильтр поиска по полю "Музыкальный инструмент"
 * (Название класса MTool выбрано для краткости - длина имени класса не может быть больше 24 символов)
 */
class QSearchFilterMTool extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('instrument');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Музыкальный инструмент";
    }
}