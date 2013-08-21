<?php

class m130821_112800_addSalaryFilterToSections extends CDbMigration
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
    
        // добавляем в большую форму поиска фильтр по разделам (мы в прошлый раз про него забыли)
        $this->bindFilter('root_section', 'sections');
        // добавляем фильтр по цене в общую форму поиска и во все разделы каталога (он будет виден только админам)
        $this->bindFilter('root_section', 'salary');
        $this->bindFilter('media_actors', 'salary');
        $this->bindFilter('models', 'salary');
        $this->bindFilter('professional_actors', 'salary');
        $this->bindFilter('children_section', 'salary');
        $this->bindFilter('student_actors', 'salary');
        $this->bindFilter('athletes', 'salary');
        $this->bindFilter('emcees', 'salary');
        $this->bindFilter('singers', 'salary');
        $this->bindFilter('musicians', 'salary');
        $this->bindFilter('dancers', 'salary');
        $this->bindFilter('doubles', 'salary');
        $this->bindFilter('twins', 'salary');
        $this->bindFilter('nopro_actors', 'salary');
        $this->bindFilter('small_people', 'salary');
        $this->bindFilter('statists', 'salary');
        $this->bindFilter('mass_actors', 'salary');
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