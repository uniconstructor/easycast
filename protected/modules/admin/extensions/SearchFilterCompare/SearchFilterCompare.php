<?php

/**
 * Виджет с результатами сравнения анкеты участника по списку критериев поиска
 * Может загружаться по AJAX
 * 
 * @todo расширить функционал этого виджета: применять его не только для анкет и ролей, но и для
 *       любых моделей системы, к которым могут быть применены критерии поиска.
 *       Для этого создать родительский класс, который решает эту задачу в общем виде,
 *       а этот виджет наследовать от него
 * @todo разрешить редактировать анкету при просмотре данных
 * @todo вывести график: насколько процентов совпадение по каждому параметру и сколько параметров
 *       из какой группы подходят по критериям
 * @todo считать отклонение для числовых значений
 */
class SearchFilterCompare extends CWidget
{
    /**
     * @var Questionary
     */
    public $questionary;
    /**
     * @var EventVacancy
     */
    public $vacancy;
    
    /**
     * @var array - используемые в роли фильтры поиска
     */
    protected $filters = array();
    /**
     * @var array - сохраненные значения формы поиска
     */
    protected $data = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        // базовый класс всех поисковых фильтров
        Yii::import('catalog.extensions.search.filters.QSearchFilterBase.QSearchFilterBase');
        // базовый класс всех поисковых обработчиков
        Yii::import('catalog.extensions.search.handlers.*');
        // модуль каталога (нужен для работы с функциями сессии)
        Yii::import('application.modules.catalog.CatalogModule');
        
        if ( isset($this->vacancy->searchFilters) )
        {
            $this->filters = $this->vacancy->searchFilters;
            $this->data    = $this->vacancy->getSearchData();
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $rows = array();
        $gridColumns = array(
            array('name' => 'filterName', 'header' => 'Фильтр поиска', 'type' => 'html'),
            array('name' => 'filterValue', 'header' => 'Значение фильтра', 'type' => 'html'),
            array('name' => 'userValue', 'header' => 'Значение в анкете', 'type' => 'html'),
        );
        foreach ( $this->filters as $filter )
        {
            $filterPassed = $this->filterIsPassed($filter);
            $userValue    = $this->questionary->getFilterFieldData($filter->shortname);
            // помечаем значение анкеты нужным цветом в зависимости от результата проверки
            if ( $filterPassed )
            {
                $userValue = '<span class="badge badge-success">'.$userValue.'</span>';
            }else
            {
                $userValue = '<span class="badge badge-important">'.$userValue.'</span>';
            }
            $row = array(
                'id'          => $filter->id,
                'filterName'  => '<b>'.$filter->name.'</b>',
                'filterValue' => $this->vacancy->getFilterDataOutput($filter->shortname),
                'userValue'   => $userValue,
            );
            $rows[] = $row;
        }
        $gridDataProvider = new CArrayDataProvider($rows);
        
        $this->widget('bootstrap.widgets.TbGridView', array(
            'dataProvider' => $gridDataProvider,
            'template'     => "{items}",
            'columns'      => $gridColumns,
        ));
    }
    
    /**
     * Проверить результат соответствия одному фильтру
     * 
     * @param CatalogFilter $filter
     * @return bool
     */
    protected function filterIsPassed($filter)
    {
        $alias = Questionary::model()->getTableAlias(true);
        $handlerClass = $filter->handlerclass;
        /* @var $handler QSearchHandlerBase */
        $handler = new $handlerClass;
        $handler->filter = $filter;
        $handler->data   = $this->data;
        
        if ( ! $criteria = $handler->getCriteria() )
        {
            return true;
        }
        $criteria->compare($alias.'.`id`', $this->questionary->id);
        // проверяем что анкета с указанным id соответствует критерию одного фильтра поиска
        return Questionary::model()->exists($criteria);
    }
}