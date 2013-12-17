<?php

// подключаем родительский класс
Yii::import('catalog.extensions.search.SearchFilters.SearchFilters');

/**
 * Большая форма поиска со всеми возможными критериями (на всю страницу)
 * Используется при поиске и при создании вакансии для мероприятий
 * (чтобы задать критерии того, кто подходит для вакансии)
 * 
 * Форма собирает себя по фрагметам, для каждого поля используя специальный виджет
 * Виджеты полей общие для фильтров и для формы поиска
 */
class QSearchForm extends SearchFilters
{
    /**
     * @var string - url по которому происходит переход после поиска
     *               Если этот параметр не задан - то перенаправления не происходит
     */
    public $redirectUrl = '';
    
    /**
     * @var array - распределение фильтров по колонкам формы поиска
     */
    protected $columnFilters = array(
        'base'   => array('name', 'gender', 'age', 'playage', 'height'),
        'looks'  => array('looktype', 'weight', 'haircolor', 'hairlength', 'eyecolor', 'body', 'shoessize', 'hastatoo'),
        'skills' => array('dancer', 'voicetimbre', 'instrument', 'sporttype', 'extremaltype', 'language', 'driver'),
    );
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     * 
     * @todo удалить
     */
    public function init()
    {
        parent::init();
        $this->mode = 'form';
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('form');
    }
    
    /**
     * Отобразить одну колонку с фильтрами
     * @param string $type - тип колонки с условиями поиска
     *                       base   - основная информация
     *                       looks  - внешность
     *                       skills - навыки
     * @return void
     */
    public function displayColumnFilters($type)
    {
        foreach ( $this->columnFilters[$type] as $filterName )
        {// перебираем все используемые фильтры поиска и отображаем те, которые должны быть в этой колонке
            /* @var $filter CatalogFilter */
            if ( ! $filter = $this->getFilterByShortName($filterName) )
            {
                continue;
            }
            // параметры отображения фильтра
            $options = array(
                'display'      => 'form',
                'filter'       => $filter,
                'searchObject' => $this->searchObject,
            );
            $path = 'catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass;
            // отображаем фильтр поиска
            echo '<li style="display:block;">';
            //echo '<div  class="search_select">';
            $this->widget($path, $options);
            //echo '</div>';
            echo '</li>';
        }
    }
    
    /**
     * Определить, включен ли фильтр в прикрепленном объекте поиска
     * @param string $name - короткое название фильтра поиска
     * @return boolean
     */
    protected function filterEnabled($name)
    {
        return true;
    }
    
    /**
     * Определить, находится ли переданный фильтр поиска в указанной колонке
     * @param CatalogFilter $filter - фильтр поиска
     * @param string $columnName - название колонки поиска
     *                             base   - основная информация
     *                             looks  - внешность
     *                             skills - навыки
     * @return bool
     */
    protected function belongsToColumn($filter, $columnName)
    {
        return in_array($filter->shortname, $this->columnFilters[$columnName]);
    }
    
    /**
     * Получить фильтр из списка по его короткому названию
     * @param unknown $name
     * @return CatalogFilter|null
     */
    protected function getFilterByShortName($name)
    {
        foreach ( $this->filters as $filter )
        {/* @var $filter CatalogFilter */
            if ( $filter->shortname == $name )
            {
                return $filter;
            }
        }
    }
    
    /**
     * Получить js-код, выполняющийся после успешного ajax-запроса из формы поиска
     *
     * @return string
     */
    protected function createSuccessSearchJs()
    {
        $redirectScript = '';
        if ( $this->redirectUrl )
        {
            $url = Yii::app()->createUrl($this->redirectUrl);
            $redirectScript = "document.location = '{$url}';return true;";
        }
        return "function(data, status){
            {$redirectScript}
            $('#{$this->searchResultsId}').html(data);
            $('#search_button').attr('class', 'btn btn-success');
            $('#search_button').val('{$this->searchButtonTitle}');
        }";
    }
}