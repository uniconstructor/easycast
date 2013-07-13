<?php

/**
 * Устанавливает первые фильтры поиска (которые были готовы раньше)
 */
class m130608_072900_installFirstFilters extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::import('application.modules.catalog.models.*');
        
        $filter = new CatalogFilter();
        $filter->shortname    = 'gender';
        $filter->widgetclass  = 'QSearchFilterGender';
        $filter->handlerclass = 'QSearchHandlerGender';
        $filter->save();
        unset($filter);
        
        $filter = new CatalogFilter();
        $filter->shortname    = 'height';
        $filter->widgetclass  = 'QSearchFilterHeight';
        $filter->handlerclass = 'QSearchHandlerHeight';
        $filter->save();
        unset($filter);
        
        $filter = new CatalogFilter();
        $filter->shortname    = 'age';
        $filter->widgetclass  = 'QSearchFilterAge';
        $filter->handlerclass = 'QSearchHandlerAge';
        $filter->save();
        unset($filter);
        
        $filter = new CatalogFilter();
        $filter->shortname    = 'weight';
        $filter->widgetclass  = 'QSearchFilterWeight';
        $filter->handlerclass = 'QSearchHandlerWeight';
        $filter->save();
        unset($filter);
    }
}