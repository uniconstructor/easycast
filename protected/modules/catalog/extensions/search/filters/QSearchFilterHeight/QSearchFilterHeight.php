<?php

/**
 * Фильтр для поиска по полю "рост"
 * @todo если не выбрано ни одно значение то в выбранном окошке писать "любой"
 */
class QSearchFilterHeight extends QSearchFilterBaseSlider
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('height');
    
    /**
     * Получить минимальное значение слайдера
     * @return int
     */
    protected function getMinValue()
    {
        return 40;
    }
    
    /**
     * Получить максимальное значение слайдера
     * @return int
     */
    protected function getMaxValue()
    {
        return 230;
    }
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Рост";
    }
}