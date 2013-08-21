<?php

/**
 * Фильтр для поиска по полю "Оплата за день"
 */
class QSearchFilterSalary extends QSearchFilterBaseSlider
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('salary');
    
    /**
     * Разрешаем поиск по цене только для админов
     * (non-PHPdoc)
     * @see QSearchFilterBase::enabled()
     */
    protected function enabled()
    {
        return Yii::app()->user->checkAccess('Admin');
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
        return 30000;
    }
    
    /**
     * Получить шаг слайдера (цена деления)
     * @return number
     */
    protected function getStepValue()
    {
        return 500;
    }
    
    /**
     * Получить текст, который отображается, когда в слайдере ничего не выбрано
     * @return string
     */
    protected function getNotSetPlaceholder()
    {
        return "Не задана";
    }
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Оплата за день";
    }
}