<?php

/**
 * Фильтр для поиска по полю "Вес"
 */
class QSearchFilterWeight extends QSearchFilterBaseSlider
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('weight');
    
    /**
     * Получить минимальное значение слайдера
     * @return int
     */
    protected function getMinValue()
    {
        return 10;
    }
    
    /**
     * Получить максимальное значение слайдера
     * @return int
     */
    protected function getMaxValue()
    {
        return 130;
    }
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Вес";
    }
}