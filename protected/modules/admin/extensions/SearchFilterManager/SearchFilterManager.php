<?php

/**
 * Виджет для ручной настройки набора критериев поиска
 * @package easycast
 * @subpackage admin
 * 
 * @todo удалять из JSON те данные поисковых критериев, которые были удалены из набора
 */
class SearchFilterManager extends CWidget
{
    /**
     * @var CatalogSection|CatalogTab|EventVacancy - объект, набор критериев которого редактируется
     */
    public $searchObject;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.modules.projects.models.*');
        
        if ( ! is_object($this->searchObject) )
        {
            throw new CException('Не задан объект для фильтров');
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // выводим оба списка
        $this->render('form');
    }
    
    /**
     * Вывести сортируемый список фильтров 
     * @param string type
     * @return void
     */
    protected function printSortableList($type)
    {
        if ( $type == 'active' )
        {// отображаем добавленные фильтры
            $widgetId     = 'pending_search_filters';
            $connectWith  = 'active_search_filters';
            $filters = $this->getActiveFilters();
        }else
        {// отображаем фильтры которые можно добавить
            $widgetId     = 'active_search_filters';
            $connectWith  = 'pending_search_filters';
            $filters = $this->getPendingFilters();
        }
        $options = CHtml::listData($filters, 'id', 'name');
        
        $this->widget('zii.widgets.jui.CJuiSortable', array(
            'items' => $options,
            // additional javascript options for the JUI Sortable plugin
            'options' => array(
                //'revert'      => true,
                //'connectWith' => $connectWith,
                'connectWith' => '.filters-list',
            ),
            'htmlOptions' => array('id' => $widgetId, 'class' => 'nav nav-list filters-list'),
            'itemTemplate' => '<li><input type="hidden" href="#" name="'.$type.'[{id}]" value="{id}">
                <a href="#">{content}</a></li>',
        ));
    }
    
    /**
     * Получить список добавленных к объекту фильтров
     * @return CatalogFilter[] 
     */
    protected function getActiveFilters()
    {
        if ( ! $filters = $this->searchObject->searchFilters )
        {
            return array();
        }
        return $filters;
    }
    
    /**
     * Получить список фильтров, которые можно добавить
     * @return CatalogFilter[]
     */
    protected function getPendingFilters()
    {
        $criteria = new CDbCriteria();
        $criteria->index = 'id';
        
        $allFilters = CatalogFilter::model()->findAll($criteria);
        if ( ! $activeFilters = $this->getActiveFilters() )
        {
            return $allFilters;
        }
        foreach ( $activeFilters as $filter )
        {
            unset($allFilters[$filter->id]);
        }
        return $allFilters;
    }
}