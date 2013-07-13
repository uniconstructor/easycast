<?php

/**
 * Фильтр поиска по полю "Музыкальное образование"
 */
class QSearchFilterMUni extends QSearchFilterBaseUni
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('musicuniversities', 'musicendyear');
    
    /**
     * @var string - тип ВУЗа (музыкальный/театральный)
     */
    protected $universityType = 'music';
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Музыкальное образование";
    }
    
    /**
     * Получить короткое название критерия поиска по вузу
     *
     * @return string
     */
    protected function getShortName()
    {
        return 'musicuniversities';
    }
    
    /**
     * Получить короткое название критерия поиска по году окончания ВУЗа
     *
     * @return string
     */
    protected function getSliderShortName()
    {
        return 'musicendyear';
    }
}