<?php

/**
 * Эта миграция устанавливает все возможные фильтры поиска в главную форму поиска на сайте
 */
class m130724_110000_installMainSearchFilters extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.extensions.ESearchScopes.models.*');
        Yii::import('application.extensions.ESearchScopes.ESearchScopes');
        
        $this->bindFilter('root_section', 'gender', 1);
        $this->bindFilter('root_section', 'age', 2);
        $this->bindFilter('root_section', 'playage', 3);
        $this->bindFilter('root_section', 'looktype', 4);
        $this->bindFilter('root_section', 'nativecountryid', 5);
        $this->bindFilter('root_section', 'height', 6);
        $this->bindFilter('root_section', 'weight', 7);
        $this->bindFilter('root_section', 'body', 8);
        $this->bindFilter('root_section', 'haircolor', 9);
        $this->bindFilter('root_section', 'hairlength', 10);
        $this->bindFilter('root_section', 'eyecolor', 11);
        $this->bindFilter('root_section', 'wearsize', 12);
        $this->bindFilter('root_section', 'shoessize', 13);
        $this->bindFilter('root_section', 'titsize', 14);
        $this->bindFilter('root_section', 'tatoo', 15);
        $this->bindFilter('root_section', 'dancer', 16);
        $this->bindFilter('root_section', 'sporttype', 17);
        $this->bindFilter('root_section', 'extremaltype', 18);
        $this->bindFilter('root_section', 'voicetype', 19);
        $this->bindFilter('root_section', 'voicetimbre', 20);
        $this->bindFilter('root_section', 'instrument', 21);
        $this->bindFilter('root_section', 'language', 22);
        $this->bindFilter('root_section', 'driver', 23);
        $this->bindFilter('root_section', 'name', 24);
    }
    
    protected function bindFilter($sectionName, $filterName, $order)
    {
        $table = "{{catalog_filter_instances}}";
    
        if ( ! $section = CatalogSection::model()->find('shortname = :shortname', array(':shortname' => $sectionName)) )
        {
            throw new CDbException($sectionName.' not found');
        }
        if ( ! $filter = CatalogFilter::model()->find('shortname = :shortname', array(':shortname' => $filterName)) )
        {
            throw new CDbException($filterName.' not found');
        }
    
        $this->insert($table, array(
            'linktype'  => 'section',
            'linkid'    => $section->id,
            'filterid'  => $filter->id,
            'visible'   => 1,
            'order'     => $order
        ));
    }
}