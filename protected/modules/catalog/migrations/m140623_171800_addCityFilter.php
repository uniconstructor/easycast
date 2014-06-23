<?php

class m140623_171800_addCityFilter extends CDbMigration
{
    public function safeUp()
    {
        Yii::app()->getModule('catalog');
        Yii::import('application.modules.catalog.models.*');
        
        $filtersTable = "{{catalog_filters}}";
        $this->insert($filtersTable, array(
            'name'         => 'Город',
            'shortname'    => 'city',
            'widgetclass'  => 'QSearchFilterCity',
            'handlerclass' => 'QSearchHandlerCity',
        ));
        $cityFilter = $this->dbConnection->createCommand()->select(array('id', 'shortname'))->
            from($filtersTable)->where("`shortname` = 'city'")->queryRow();
        
        
        // привязываем поиск по городу ко всем разделам
        $sectionsTable = '{{catalog_sections}}';
        $sections = $this->dbConnection->createCommand()->select(array('id', 'shortname'))->
            from($sectionsTable)->queryAll();
        foreach ( $sections as $section )
        {
            $this->bindFilter($cityFilter['id'], $section['id'], 0, 'section');
        }

        // привязываем поиск по городу ко всем ролям
        $vacanciesTable = '{{event_vacancies}}';
        $vacancies = $this->dbConnection->createCommand()->select(array('id', 'searchdata'))->
            from($vacanciesTable)->queryAll();
        foreach ( $vacancies as $vacancy )
        {
            $this->bindFilter($cityFilter['id'], $vacancy['id'], 0, 'vacancy');
        }
    }
    
    protected function bindFilter($filterId, $linkId, $visible=1, $linkType='section')
    {
        $table = "{{catalog_filter_instances}}";
    
        $criteria = new CDbCriteria();
        $criteria->compare('linktype', $linkType);
        $criteria->compare('linkid', $linkId);
        $criteria->compare('filterid', $filterId);
        $order = (1 + CatalogFilterInstance::model()->count($criteria));
    
        $this->insert($table, array(
            'linktype'  => $linkType,
            'linkid'    => $linkId,
            'filterid'  => $filterId,
            'visible'   => $visible,
            'order'     => $order,
        ));
    }
}