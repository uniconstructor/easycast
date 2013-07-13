<?php

/**
 * Класс виджета поиска по полю "Страна рождения"
 */
class QSearchFilterCountry extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('nativecountryid');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Страна рождения";
    }
    
    /**
     * Получить список стран для выпадающего меню
     *
     * @return array
     * 
     * @todo переписать алгоритм на более оптимальный после переработки плагина со странами
     */
    protected function getMenuVariants()
    {
        Yii::import('application.extensions.CountryCitySelectorRu.models.*');
        $variants = array();
        
        $criteria = new CDbCriteria();
        $criteria->order = '`name` ASC';
        $countries = CSGeoCountry::model()->findAll($criteria);
        
        foreach ( $countries as $country )
        {
            $variants[$country->id] = $country->name;
        }
        
        return $variants;
    }
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBaseSelect2::createSelect2Options()
     */
    protected function createSelect2Options()
    {
        $options = parent::createSelect2Options();
        $options['placeholder'] = 'Введите первые буквы';
        
        return $options;
    }
}