<?php

class m140612_052300_addStatusFilter extends CDbMigration
{
    public function safeUp()
    {
        Yii::app()->getModule('catalog');
        Yii::import('application.modules.catalog.models.*');
        
        $filtersTable = "{{catalog_filters}}";
        $statusFilter = $this->dbConnection->createCommand()->select(array('id', 'shortname'))->
            from($filtersTable)->where("`shortname` = 'status'")->queryRow();
        $iconlistFilter = $this->dbConnection->createCommand()->select(array('id', 'shortname'))->
            from($filtersTable)->where("`shortname` = 'iconlist'")->queryRow();
        $addcharFilter = $this->dbConnection->createCommand()->select(array('id', 'shortname'))->
            from($filtersTable)->where("`shortname` = 'addchar'")->queryRow();
        
        
        // привязываем статус анкеты ко всем разделам
        $sectionsTable = '{{catalog_sections}}';
        $sections = $this->dbConnection->createCommand()->select(array('id', 'shortname'))->
            from($sectionsTable)->queryAll();
        foreach ( $sections as $section )
        {
            $this->bindFilter($statusFilter['id'], $section['id'], 0, 'section');
            $this->bindFilter($addcharFilter['id'], $section['id'], 0, 'section');
        }
        // привязываем поиск по разделам к корневому разделу каталога (для большой формы поиска)
        $this->bindFilter($iconlistFilter['id'], 1, 1, 'section');

        
        // привязываем статус анкеты ко всем ролям
        $vacanciesTable = '{{event_vacancies}}';
        $vacancies = $this->dbConnection->createCommand()->select(array('id', 'searchdata'))->
            from($vacanciesTable)->queryAll();
        foreach ( $vacancies as $vacancy )
        {
            $this->bindFilter($statusFilter['id'], $vacancy['id'], 0, 'vacancy');
            $this->bindFilter($iconlistFilter['id'], $vacancy['id'], 1, 'vacancy');
            $searchData = unserialize($vacancy['searchdata']);
            
            if ( is_array($searchData) )
            {// в каждую роль добавляем условие поиска по статусу
                $searchData[CatalogModule::SEARCH_FIELDS_PREFIX.'status'] = array(
                    'status' => array('active', 'pending', 'rejected'),
                );
                // и обновляем условия поиска
                $newSearchData = serialize($searchData);
                $this->update($vacanciesTable,
                    array('searchdata' => $newSearchData),
                    array('`id` = '.$vacancy['id'])
                );
                echo 'vacancy: '.$vacancy['id']."\n";
            }
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