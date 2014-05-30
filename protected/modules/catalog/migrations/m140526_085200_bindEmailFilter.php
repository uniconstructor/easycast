<?php

class m140526_085200_bindEmailFilter extends CDbMigration
{
    public function safeUp()
    {
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.extensions.ESearchScopes.models.*');
        Yii::import('application.extensions.ESearchScopes.ESearchScopes');
        Yii::app()->getModule('catalog');
        
        // добавляем фильтр по email в общую форму поиска и во все разделы каталога (он будет виден только админам)
        $this->bindFilter('root_section', 'email');
        $this->bindFilter('media_actors', 'email');
        $this->bindFilter('models', 'email');
        $this->bindFilter('professional_actors', 'email');
        $this->bindFilter('children_section', 'email');
        $this->bindFilter('student_actors', 'email');
        $this->bindFilter('athletes', 'email');
        $this->bindFilter('emcees', 'email');
        $this->bindFilter('singers', 'email');
        $this->bindFilter('musicians', 'email');
        $this->bindFilter('dancers', 'email');
        $this->bindFilter('doubles', 'email');
        $this->bindFilter('twins', 'email');
        $this->bindFilter('nopro_actors', 'email');
        $this->bindFilter('small_people', 'email');
        $this->bindFilter('statists', 'email');
        $this->bindFilter('mass_actors', 'email');
    }
    
    protected function bindFilter($sectionName, $filterName, $order=null)
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
        if ( ! $order )
        {
            $criteria = new CDbCriteria();
            $criteria->compare('linktype', 'section');
            $criteria->compare('linkid', $section->id);
            $criteria->compare('filterid', $filter->id);
    
            $order = (1 + CatalogFilterInstance::model()->count($criteria));
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