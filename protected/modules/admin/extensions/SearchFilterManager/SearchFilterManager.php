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
     * @var string - адрес обработчика, сохраняющего критерии поиска
     */
    public $actionUrl;
    
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
        $js = '$("#active_search_filters").sortable({
                update: function(event, ui){
                    $("#activeFilters").val($("#active_search_filters").sortable("toArray"));
                    //console.log($("#activeFilters").val());
                },
            }).disableSelection();';
        Yii::app()->clientScript->registerScript('ecCollectFilters', $js, CClientScript::POS_END);
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
        $styles = 'nav nav-list filters-list';
        $arrow  = '';
        if ( $type == 'active' )
        {// отображаем добавленные фильтры
            $widgetId = 'active_search_filters';
            $filters  = $this->getActiveFilters();
            //$styles  .= 'alert alert-success';
        }else
        {// отображаем фильтры которые можно добавить
            $widgetId  = 'pending_search_filters';
            $filters   = $this->getPendingFilters();
            //$arrow   = '<i style="padding-top:5px;" class="icon-arrow-right pull-right"></i>';
            //$styles .= 'alert alert-info';
        }
        $options = CHtml::listData($filters, 'id', 'name');
        
        $this->widget('zii.widgets.jui.CJuiSortable', array(
            'items' => $options,
            // additional javascript options for the JUI Sortable plugin
            'options' => array(
                'connectWith' => '.filters-list',
            ),
            'tagName' => 'ol',
            'htmlOptions' => array('id' => $widgetId, 'class' => $styles),
            'itemTemplate' => '<li id="{id}"><a href="#">{content}'.$arrow.'</a></li>',
        ));
        // <input type="hidden" class="filterid" name="'.$type.'[]" value="{id}">
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
        $criteria->order = "`name` ASC";
        
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