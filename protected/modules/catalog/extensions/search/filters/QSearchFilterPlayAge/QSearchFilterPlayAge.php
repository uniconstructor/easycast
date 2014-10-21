<?php

/**
 * Фильтр для поиска по полю "Игровой возраст"
 */
class QSearchFilterPlayAge extends QSearchFilterBaseSlider
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('playage');
    
    /**
     * 
     * @see QSearchFilterBase::enabled()
     */
    public function enabled()
    {
        return true;
    }
    /**
     * @see QSearchFilterBase::visible()
     */
    public function visible()
    {
        return false;
    }
    
    /**
     * Получить минимальное значение слайдера
     * @return int
     */
    protected function getMinValue()
    {
        return 0;
    }
    
    /**
     * Получить максимальное значение слайдера
     * @return int
     */
    protected function getMaxValue()
    {
        return 65;
    }
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Игровой возраст";
    }
}