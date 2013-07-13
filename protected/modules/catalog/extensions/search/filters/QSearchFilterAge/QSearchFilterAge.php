<?php

/**
 * Фильтр для поиска по полю "Возраст"
 */
class QSearchFilterAge extends QSearchFilterBaseSlider
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('age');
    
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
        return "Возраст";
    }
}