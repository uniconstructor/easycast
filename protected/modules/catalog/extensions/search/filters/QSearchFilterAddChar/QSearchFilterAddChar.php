<?php

/**
 * Фильтр поиска по полю "Дополнительные характеристики внешности"
 */
class QSearchFilterAddChar extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('addchar');
    
    /**
     * @see QSearchFilterBase::enabled()
     */
    public function enabled()
    {
        return Yii::app()->user->checkAccess('Admin');
    }
    
    /**
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Доп. характеристики";
    }
}