<?php

/**
 * Виджет для отображения списка фильтров поиска в каталоге
 * 
 * Для каждого раздела каталога набор фильтров индивидуален, поэтому виджет собирает
 * форму из нужных фрагментов. Фрагменты фильтров также используются в форме поиска
 * 
 * @todo перенести весь JS во внешние файлы
 */
class QSearchFilters extends CWidget
{
    /**
     * @var string - режим отображения фильтров (filter/search)
     */
    public $mode = 'filter';
    /**
     * @var CatalogSection - раздел анкеты в котором отображаются фильтры
     */
    public $section;
    
    /**
     * @var boolean - отображать ли заголовок формы?
     */
    public $displayTitle = true;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        // Подключаем базовые классы виджетов-фильтров поиска от которых наследуются все остальные фильтры
        // Самый базовый класс
        Yii::import('catalog.extensions.search.filters.QSearchFilterBase.QSearchFilterBase');
        // Базовый класс для всех виджетов использующих слайдер
        Yii::import('catalog.extensions.search.filters.QSearchFilterBaseSlider.QSearchFilterBaseSlider');
        // Базовый класс для всех виджетов использующих плагин "select2"
        Yii::import('catalog.extensions.search.filters.QSearchFilterBaseSelect2.QSearchFilterBaseSelect2');
        // Базовый класс для виджетов поиска по ВУЗам
        Yii::import('catalog.extensions.search.filters.QSearchFilterBaseUni.QSearchFilterBaseUni');
        
        if ( ! is_object($this->section) AND $this->mode == 'filter' )
        {
            throw new CHttpException('500', 'Не указан раздел для фильтров');
        }
        $this->registerSearchResultsRefreshScript();
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->displayTitle )
        {
            echo $this->getFilterTitle();
        }
        foreach ( $this->section->filterinstances as $instance )
        {// Перебираем все фильтры раздела и для каждого создаем виджет
            $this->displayFilter($instance->filter);
        }
        // Добавляем внизу кнопки "очистить" и "найти"
        $this->displayButtons();
    }
    
    /**
     * Получить заголовок для формы фильтров поиска
     *
     * @return string
     * 
     * @todo языковые строки
     */
    protected function getFilterTitle()
    {
        return "<h4>Поиск в разделе &quot;{$this->section->name}&quot;</h4>";
    }
    
    /**
     * Отобразить один сворачивающийся раздел с виджетом-фильтром
     * @param CatalogFilter $filter - объект фильтра поиска, из таблицы ctalog_filters
     * 
     * @return array
     */
    protected function displayFilter($filter)
    {
        $panel = array();
        
        // Задаем путь к виджету фрагмента поиска и настройкам для него
        $path = 'catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass;
        $options = array(
            'section' => $this->section,
            'filter'  => $filter,
            'display' => $this->mode,
        );
        // Получаем заголовок и код виджета
        $this->widget($path, $options);
    }
    
    /**
     * Отобразить кнопки "очистить" и "найти"
     * 
     * @return null
     * @todo обработать ошибки AJAX
     */
    protected function displayButtons()
    {
        // Кнопка "Найти"
        // search_results
        $this->displaySearchButton();
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
        // Кнопка "Очистить"
        $this->displayClearButton();
    }
    
    /**
     * Отобразить кнопку "найти"
     * 
     * @return null
     */
    protected function displaySearchButton()
    {
        $searchUrl = Yii::app()->createUrl('/catalog/catalog/ajaxSearch');
        
        // Перед отправкой поискового запроса пристыковываем к нему данные из поисковой формы в формате json
        // Плюс к этому, на время запоса выключаем кнопку поиска чтобы пользователь видел что процесс идет
        $beforeSendJs = "function(jqXHR, settings){
            $('#search_button').attr('class', 'btn btn-disabled');
            $('#search_button').val('Ищем...');
            
            var ecSearchData = {};
            $('body').trigger('collectData', [ecSearchData]);
            //console.log(ecSearchData);
        
            var encodedData = JSON.stringify(ecSearchData);
            settings.data = settings.data + '&data=' + encodedData;
            
            return true;
        }";
        // после ответа на запрос обновляем содержимое результатов поиска
        $successJs = "function(data, status){
            $('#search_results').html(data);
        
            $('#search_button').attr('class', 'btn btn-success');
            $('#search_button').val('Найти');
        }";
        
        // Задаем настройки для поискового AJAX-запроса
        $ajaxData = array(
            'sectionId' => $this->getSectionId(),
            'mode'      => $this->mode,
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken);
        $ajaxOptions = array(
            'url'         => $searchUrl,
            'data'        => $ajaxData,
            'cache'       => false,
            'type'        => 'post',
            'beforeSend'  => $beforeSendJs,
            'success'     => $successJs,
        );
        
        echo CHtml::ajaxButton('Найти', $searchUrl, $ajaxOptions, array(
            'class' => 'btn btn-success',
            'id'    => 'search_button'
        ));
    }
    
    /**
     * отобразить кнопку "очистить"
     * 
     * @return null
     */
    protected function displayClearButton()
    {
        $clearUrl = Yii::app()->createUrl('/catalog/catalog/clearSessionSearchData');
        $clearJs = "function(data, status){
            $('body').trigger('clearSearch');
        }";
        $ajaxData = array(
            'sectionId' => $this->getSectionId(),
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken);
        $ajaxOptions = array(
            'url'        => $clearUrl,
            'data'       => $ajaxData,
            'type'       => 'post',
            'beforeSend' => $clearJs,
        );
        
        echo CHtml::ajaxButton('Очистить', $clearUrl, $ajaxOptions, array(
            'class' => 'btn btn-primary',
            'id'    => 'clear_search'
        ));
    }
    
    /**
     * Получить скрипт, обновляющий результаты поиска в ответ на AJAX-запрос
     * 
     * @return null
     */
    protected function registerSearchResultsRefreshScript()
    {
        $js = "";
        Yii::app()->clientScript->registerScript('_ecRefreshSearchResultsJS#', $js, CClientScript::POS_END);
    }
    
    /**
     * Получить id текущего раздела каталога (если отображаются фильтры) или 0 (если отображается большая форма)
     * @return number
     * @return null
     */
    protected function getSectionId()
    {
        if ( is_object($this->section) )
        {
            return $this->section->id;
        }
        
        return 0;
    }
}