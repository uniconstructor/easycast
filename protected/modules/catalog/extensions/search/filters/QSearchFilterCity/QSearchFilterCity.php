<?php

/**
 * Фильтр поиска по городу
 * 
 * @todo сделать поиск по всему списку городов
 */
class QSearchFilterCity extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('cityid');
    
    /**
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Город";
    }
    
    /**
     * @see QSearchFilterBase::visible()
     */
    public function visible()
    {
        return Yii::app()->user->checkAccess('Admin');
    }
    
    /**
     * @see QSearchFilterBaseSelect2::getMenuVariants()
     */
    protected function getMenuVariants()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('regionid', '4312');
        
        $models = CSGeoCity::model()->findAll($criteria);
        
        return CHtml::listData($models, 'id', 'name');
    }
}