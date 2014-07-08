<?php

/**
 * Фильтр поиска по региону
 */
class QSearchFilterRegion extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('regionid');
    
    /**
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Регион";
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
        $criteria->compare('countryid', '3159');
    
        $models = CSGeoRegion::model()->findAll($criteria);
    
        return CHtml::listData($models, 'id', 'name');
    }
}