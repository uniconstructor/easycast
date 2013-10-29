<?php

/**
 * Виджет для отображения списка фильтров поиска в каталоге
 * 
 * Для каждого раздела каталога набор фильтров индивидуален, поэтому виджет собирает
 * форму из нужных фрагментов. Фрагменты фильтров также используются в форме поиска
 * 
 * @todo перенести весь JS во внешние файлы
 * @todo убрать поле section, заменить его более общим (объект, обладающий фильтрами)
 * @todo добавить поле filtersOptions - для того чтобы можно было задавать отдельные настройки для каждого фильтра
 *       при выводе всего списка фильтров
 */
class SearchFilters extends CWidget
{
    /**
     * @var CActiveRecord - модель к которой прикреплены критерии поиска 
     *                  Этот объект обязательно должен обладать отношением (Relation)
     *                  которое назызывается searchFilters, которое содержит 
     *                  используемые фильтры поиска
     */
    public $searchObject;
    
    /**
     * @var string - режим отображения фильтров:
     *               filter - фильтр в разделе каталога
     *               search - большая форма поиска
     *               vacancy - критерии подбора участников для вакансии
     * @deprecated не используется, удалить при рефакторинге
     */
    public $mode = 'filter';
    
    /**
     * @var string - источник данных для формы (откуда будут взяты значения по умолчанию)
     *               Возможные значения:
     *               'session' - данные берутся из сессии (используется во всех формах поиска)
     *               'db' - данные берутся из базы (используется при сохранении критериев вакансии и т. п.)
     */
    public $dataSource = 'session';
    
    /**
     * @var array - массив ссылок на используемые фильтры (CatalogFilterInstance)
     * @deprecated больше не используется, удалить при рефакторинге
     */
    public $filterInstances = array();
    
    /**
     * @var CatalogSection - раздел анкеты в котором отображаются фильтры
     * @deprecated не используется, удалить при рефакторинге
     *             Вместо этого поля теперь используется searchObject
     */
    public $section;
    
    /**
     * @var string - по какому адресу отправлять поисковый ajax-запрос
     */
    public $searchUrl = '/catalog/catalog/ajaxSearch';
    
    /**
     * @var string - по какому адресу отправлять запрос на очистку данных формы
     */
    public $clearUrl = '/catalog/catalog/clearSessionSearchData';
    
    /**
     * @var boolean - отображать ли заголовок формы?
     */
    public $displayTitle = true;
    
    /**
     * @var string надпись на кнопке поиска в обычном состоянии
     */
    public $searchButtonTitle = 'Найти';
    
    /**
     * @var string надпись на кнопке поиска во время выполнения поиска
     */
    public $searchProgressTitle = 'Ищем...';
    
    /**
     * @var string - id html-тега, в котором обновляются результаты поиска
     *               (виджет класса QSearchResults)
     */
    public $searchResultsId = 'search_results';
    
    /**
     * @var array - используемые фильтры поиска (массив объектов CatalogFilter)
     */
    protected $filters = array();
    
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
        
        if ( ! empty($this->filterInstances) )
        {// список фильтров задан извне - соберем их в массив
            foreach ( $this->filterInstances as $instance )
            {
                $this->filters[] = $instance->filter;
            }
        }elseif ( is_object($this->section) )
        {// список фильтров нужно взять из раздела
            foreach ( $this->section->filterinstances as $instance )
            {
                $this->filters[] = $instance->filter;
            }
        }
        if ( empty($this->filters) AND ! is_object($this->section) AND $this->mode == 'filter' )
        {// список фильтров не задан извне, и не содержится в объекте
            throw new CHttpException('500', 'Не указан раздел для фильтров');
        }
        
        // регистрируем скрипт обновляющий результаты поиска при изменении данных в форме (пока не готов)
        // $this->registerSearchResultsRefreshScript();
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->displayTitle )
        {// Определяем, нужно ли показывать заголовок над всеми фильтрами
            echo $this->getFilterTitle();
        }
        foreach ( $this->filters as $filter )
        {// Перебираем все фильтры раздела и для каждого создаем виджет
            $this->displayFilter($filter);
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
        if ( $this->mode == 'form' )
        {
            return "<h4>Условия</h4>";
        }else
        {
            return "<h4>Поиск в разделе &quot;{$this->section->name}&quot;</h4>";
        }
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
        $options = $this->getDisplayFilterOptions($filter);
        // Получаем заголовок и код виджета
        $this->widget($path, $options);
    }
    
    /**
     * Получить параметры для отображения всех виджетов (фильтров) поиска
     * 
     * @return multitype:unknown CatalogSection string 
     * @return null
     */
    protected function getDisplayFilterOptions($filter)
    {
        return array(
            'section' => $this->section,
            'filter'  => $filter,
            'display' => $this->mode,
            'dataSource' => $this->dataSource,
        );
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
        $this->displaySearchButton();
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
        // Кнопка "Очистить"
        $this->displayClearButton();
    }
    
    /**
     * Отобразить кнопку "найти"
     * 
     * @return null
     * 
     * @todo сделать id тега с результатами поиска настройкой
     */
    protected function displaySearchButton()
    {
        $ajaxUrl = Yii::app()->createUrl($this->searchUrl);
        
        // Перед отправкой поискового запроса пристыковываем к нему данные из поисковой формы в формате json
        // Плюс к этому, на время запоса выключаем кнопку поиска чтобы пользователь видел что процесс идет
        $beforeSendJs = "function(jqXHR, settings){
            $('#search_button').attr('class', 'btn btn-disabled');
            $('#search_button').val('{$this->searchProgressTitle}');
            
            var ecSearchData = {};
            $('body').trigger('collectData', [ecSearchData]);
            //console.log(ecSearchData);
        
            var encodedData = JSON.stringify(ecSearchData);
            settings.data = settings.data + '&data=' + encodedData;
            
            return true;
        }";
        // после ответа на запрос обновляем содержимое результатов поиска
        $successJs = $this->createSuccessSearchJs();
        
        // Задаем настройки для поискового AJAX-запроса
        $ajaxOptions = array(
            'url'         => $ajaxUrl,
            'data'        => $this->getAjaxSearchParams(),
            'cache'       => false,
            'type'        => 'post',
            'beforeSend'  => $beforeSendJs,
            'success'     => $successJs,
        );
        
        echo CHtml::ajaxButton($this->searchButtonTitle, $ajaxUrl, $ajaxOptions, array(
            'class' => 'btn btn-success',
            'id'    => 'search_button'
        ));
    }
    
    /**
     * Получить параметры POST-запроса, отсылаемые вместе с данными поисковой формы
     * 
     * @return array
     */
    protected function getAjaxSearchParams()
    {
        return array(
            'sectionId' => $this->getSectionId(),
            'mode'      => $this->mode,
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken);
    }
    
    /**
     * Получить js-код, выполняющийся после успешного ajax-запроса из формы поиска
     * 
     * @return string
     */
    protected function createSuccessSearchJs()
    {
        return "function(data, status){
            $('#{$this->searchResultsId}').html(data);
        
            $('#search_button').attr('class', 'btn btn-success');
            $('#search_button').val('{$this->searchButtonTitle}');
        }";
    }
    
    /**
     * Отобразить кнопку "очистить"
     * 
     * @return null
     */
    protected function displayClearButton()
    {
        $clearUrl = Yii::app()->createUrl($this->clearUrl);
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
     * 
     * @todo дописать позже
     */
    protected function registerSearchResultsRefreshScript()
    {
        //$js = "";
        //Yii::app()->clientScript->registerScript('_ecRefreshSearchResultsJS#', $js, CClientScript::POS_END);
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