<?php

/**
 * Фильтр поиска по полю "Театральное образование" (Actor University)
 */
class QSearchFilterAUni extends QSearchFilterBaseUni
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('actoruniversities', 'actorendyear');
    
    /**
     * @var string - тип ВУЗа (музыкальный/театральный)
     */
    protected $universityType = 'theatre';
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Театральное образование";
    }
    
    /**
     * Получить короткое название критерия поиска по вузу
     *
     * @return string
     */
    protected function getShortName()
    {
        return 'actoruniversities';
    }
    
    /**
     * Получить короткое название критерия поиска по году окончания ВУЗа
     *
     * @return string
     */
    protected function getSliderShortName()
    {
        return 'actorendyear';
    }
}