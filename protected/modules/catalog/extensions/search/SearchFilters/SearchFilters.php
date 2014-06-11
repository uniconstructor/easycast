<?php

/**
 * Виджет для отображения списка фильтров поиска
 * Все формы поиска наследуются от этого виджета
 * 
 * Для каждой формы (например для раздела каталога) набор фильтров индивидуален, поэтому виджет сам собирает
 * форму из нужных фрагментов.
 * 
 * @todo перенести весь JS во внешние файлы
 * @todo убрать поле section, заменить его более общим объектом searchObject (объект, обладающий фильтрами)
 * @todo прописать использование countData
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
     *               filter - фильтр в разделе каталога (набор фильтров берется из раздела каталога)
     *               search[не используется] - большая форма поиска (@todo больше не используется, оставлено для совместимости: используйте значение 'form')
     *               form - большая форма поиска (набор фильтров берется из первого (корневого) раздела каталога "все")
     *               vacancy[не используется] - критерии подбора участников для вакансии
     */
    public $mode       = 'filter';
    /**
     * @var string - источник данных для формы (откуда будут взяты значения по умолчанию)
     *               Возможные значения:
     *               'session'  - данные берутся из сессии (используется во всех формах поиска)
     *               'db'       - данные берутся из базы (используется при сохранении критериев вакансии и т. п.)
     *               'external' - данные установлены принудительно извне
     */
    public $dataSource = 'session';
    /**
     * @var array - массив со значениями по умолчанию для формы
     *              (только если $this->dataSource = 'external')
     *              Ключи массива - это префиксы имен фильтров 
     */
    public $data       = array();
    /**
     * @var array - массив ссылок на используемые фильтры (CatalogFilterInstance)
     * @deprecated больше не используется, удалить при рефакторинге
     */
    public $filterInstances = array();
    /**
     * @var CatalogSection - раздел анкеты в котором отображаются фильтры
     * @deprecated не используется, удалить при рефакторинге. Вместо этого поля теперь используется searchObject.
     */
    public $section;
    /**
     * @var string - по какому адресу отправлять поисковый ajax-запрос
     */
    public $searchUrl = '/catalog/catalog/ajaxSearch';
    /**
     * @var string - по какому адресу отправлять запрос на очистку данных формы
     */
    public $clearUrl  = '/catalog/catalog/clearSessionSearchData';
    /**
     * @var string - ссылка на возврат к большой форме поиска
     *               Если не задана - то кнопка "вернуться в расширенный поиск" не показывается
     */
    public $backToFormUrl = '';
    /**
     * @var boolean - отображать ли заголовок формы?
     */
    public $displayTitle  = true;
    /**
     * @var string надпись на кнопке поиска в обычном состоянии
     */
    public $searchButtonTitle   = 'Найти';
    /**
     * @var string надпись на кнопке поиска во время выполнения поиска
     */
    public $searchProgressTitle = 'Ищем...';
    /**
     * @var string - id html-тега, в котором обновляются результаты поиска
     *               (виджет класса QSearchResults)
     */
    public $searchResultsId     = 'search_results';
    /**
     * @var array - используемые фильтры поиска (массив объектов CatalogFilter)
     */
    public $filters = array();
    /**
     * @var array - массив настроек для каждого фильтра. Ключом массива является короткое названия фильтра,
     *              а значением - настройки фильтра.
     *              Пример:
     *              ...
     *              'sporttype' => array(
     *                  'dataSource' => 'external',
     *                  'data'       => array(
     *                      'sporttype' => array('wrestling', 'bike'),
     *                      ),
     *                  'refreshDataOnChange' => true,
     *                  ...
     *              ),
     *              ...
     */
    public $filterOptions = array();
    /**
     * @var string - название jQuery события, посылаемого при очистке всей формы
     */
    public $clearSearchEvent  = 'clearSearch';
    /**
     * @var string - название jQuery события, посылаемого при сборе данных со всей формы
     */
    public $collectDataEvent  = 'collectData';
    /**
     * @var string - название jQuery события, посылаемого для обновления результатов поиска
     */
    public $refreshDataEvent  = 'refreshData';
    /**
     * @var string - название jQuery события, посылаемого для подсчета обновления количества подходящих участников
     */
    public $countDataEvent    = 'countData';
    /**
     * @var bool - обновлять результаты поиска при каждом изменении критериев поиска
     */
    public $refreshDataOnChange  = true;
    /**
     * @var bool - обновлять количество найденых участников при каждом изменении критериев поиска
     */
    public $countDataOnChange    = true;
    /**
     * @var array - параметры отображения для кнопки "Найти"
     */
    public $searchButtonHtmlOptions  = array(
        'class' => 'btn btn-success',
        'id'    => 'search_button',
    );
    /**
     * @var array - параметры отображения для кнопки "Очистить"
     */
    public $clearButtonHtmlOptions   = array(
        'class' => 'btn btn-primary',
        'id'    => 'clear_search',
    );
    /**
     * @var array - параметры отображения для кнопки "Очистить"
     */
    public $disabledButtonHtmlOptions = array(
        'class' => 'btn btn-disabled',
    );
    /**
     * @var array - параметры отображения для кнопки "Вернуться в расширенный поиск"
     */
    public $backToFormButtonHtmlOptions = array(
        'class' => 'btn btn-warning',
    );
    
    /**
     * @var array - допустимые режимы отображения виджета
     *              form    - большая форма поиска: критерии поиска не сворачиваются, список разделов отображается
     *                        как большая полоска со списком кнопок наверху
     *              filter  - набор фильтров поиска для раздела каталога или для поиска по всей базе
     *                        Набор фильтров отображается как вертикальная колонка поисковых условий справа
     *              vacancy - набор фильтров для создания критериев отбора людей на роль
     *                        Используется в админке при создании роли.
     *                        @todo использовать в онлайн-кастинге вместо filter как сейчас
     *              section - набор фильтров для создания/редактирования условий раздела каталога
     *                        Набор фильтров отображается как вертикальная колонка поисковых условий справа
     *              tab     - набор фильтров для создания/редактирования условий вкладки в разделе каталога
     *                        Набор фильтров отображается как вертикальная колонка поисковых условий справа
     */
    protected $displayModes = array('form', 'filter', 'vacancy', 'section', 'tab');
    
    /**
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
        
        if ( $this->searchObject )
        {// задан объект поиска (раздел каталога, роль, и т. д.) - извлекаем набор фильтров из него
            if ( ! $this->filters AND ! $this->searchObject->searchFilters )
            {
                throw new CException('В указанном объекте отсутствуют прикрепленные критерии поиска');
            }
            if ( ! $this->filters )
            {
                $this->filters = $this->searchObject->searchFilters;
            }
        }elseif ( ! empty($this->filterInstances) )
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
            $this->searchObject = $this->section;
        }
        if ( empty($this->filters) AND ! is_object($this->section) AND $this->mode == 'filter' )
        {// список фильтров не задан извне, и не содержится в объекте
            throw new CException('Не указан раздел для фильтров');
        }
        // регистрируем скрипт обновляющий результаты поиска при изменении данных в форме
        $this->registerSearchResultsRefreshScript();
        
        parent::init();
    }
    
    /**
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
     * Установить собственный набор фильтров вместо того, который был привязан к объекту изначально
     * (используется в случаях вроде создания разделов каталога, когда набор фильтров, находящийся в
     * разделе и набор фильтров, необходимый для того чтобы создать критерии отбора людей на роль - 
     * это разные вещи)
     * @param CatalogFilter[] $filters - новый массив фильтров поиска, который будет использоваться в форме
     * @return void
     */
    public function setFilters($filters)
    {
        if ( ! $filters )
        {
            throw new CException('Задан пустой массив фильтров поиска');
        }
        $this->filters = $filters;
    }

    /**
     * Получить установленные в фильтре значения по умолчанию
     * @param CatalogFilter $filter - фильтр поиска
     * @return array|null
     * 
     * @todo перенести загрузку значений из сессии и базы сюда же,
     *       убрать этот функционал из класса QSearchFilterBase
     * @deprecated
     */
    protected function loadFilterData($filter)
    {
        $data = array();
        $namePrefix = CatalogModule::SEARCH_FIELDS_PREFIX.$filter->shortname;
        
        switch ( $this->dataSource )
        {
            case 'external':
                if ( isset($this->data[$namePrefix]) AND ! empty($this->data[$namePrefix]) )
                {
                    return $this->data[$namePrefix];
                }
            break;
        }
        
        return $data;
    }
    
    /**
     * Получить заголовок для формы фильтров поиска
     * @return string
     * 
     * @todo языковые строки
     * @todo переименовать в getFiltersTitle
     */
    protected function getFilterTitle()
    {
        if ( ! $this->displayTitle OR ! isset($this->searchObject) )
        {// заголовок над всеми фильтрами вообще отображать не нужно
            return '';
        }
        if ( $this->mode === 'form' OR ( isset($this->searchObject->id) AND $this->searchObject->id == 1 ) )
        {
            return "<h4>Условия</h4>";
        }elseif ( isset($this->searchObject->id) )
        {
            return "<h4>Поиск в разделе &quot;{$this->searchObject->name}&quot;</h4>";
        }else
        {
            return '';
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
        $path    = 'catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass;
        $options = $this->getDisplayFilterOptions($filter);
        // Получаем заголовок и код виджета
        $this->widget($path, $options);
    }
    
    /**
     * Получить параметры для отображения всех виджетов (фильтров) поиска
     * @param CatalogFilter $filter - отображаемый фильтр поиска
     * @return array
     */
    protected function getDisplayFilterOptions($filter)
    {
        $defaults = array(
            'searchObject'        => $this->searchObject,
            'filter'              => $filter,
            'display'             => $this->mode,
            'dataSource'          => $this->dataSource,
            'refreshDataOnChange' => $this->refreshDataOnChange,
            'refreshDataEvent'    => $this->refreshDataEvent,
            'countDataOnChange'   => $this->countDataOnChange,
            'countDataEvent'      => $this->countDataEvent,
            'collectDataEvent'    => $this->collectDataEvent,
            'clearUrl'            => $this->clearUrl,
        );
        if ( isset($this->filterOptions[$filter->shortname]) AND is_array($this->filterOptions[$filter->shortname]) )
        {// для этого фильтра поиска заданы индивидуальные настройки
            return CMap::mergeArray($defaults, $this->filterOptions[$filter->shortname]);
        }
        return $defaults;
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
        // кнопка "Вернуться в расширенный поиск" (если нужна)
        $this->displayBackToFormButton();
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
        $ajaxUrl     = Yii::app()->createUrl($this->searchUrl);
        $ajaxOptions = $this->getAjaxSearchOptions();
        
        echo CHtml::ajaxButton($this->searchButtonTitle, $ajaxUrl, $ajaxOptions, $this->searchButtonHtmlOptions);
    }
    
    /**
     * Получить параметры POST-запроса, отсылаемые вместе с данными поисковой формы
     * 
     * @return array
     */
    protected function getAjaxSearchParams()
    {
        return array(
            'searchObjectId' => $this->getSearchObjectId(),
            'mode'           => $this->mode,
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
        );
    }
    
    /**
     * Получить полный набор параметров для создания AJAX-запроса
     * @return array
     */
    protected function getAjaxSearchOptions()
    {
        $ajaxUrl = Yii::app()->createUrl($this->searchUrl);
        // Перед отправкой поискового запроса пристыковываем к нему данные из поисковой формы в формате json
        // Плюс к этому, на время запоса выключаем кнопку поиска чтобы пользователь видел что процесс идет
        $beforeSendJs = $this->createBeforeSearchJs();
        // после ответа на запрос обновляем содержимое результатов поиска
        $successJs    = $this->createSuccessSearchJs();
        
        // Задаем настройки для поискового AJAX-запроса
        $ajaxOptions = array(
            'url'         => $ajaxUrl,
            'data'        => $this->getAjaxSearchParams(),
            'cache'       => false,
            'type'        => 'post',
            'beforeSend'  => $beforeSendJs,
            'success'     => $successJs,
        );
        return $ajaxOptions;
    }
    
    /**
     * Создать скрипт, инициирующий AJAX-запрос поиска
     * @return string
     */
    protected function createBeforeSearchJs()
    {
        $searchButtonId = $this->searchButtonHtmlOptions['id'];
        $disabledClass  = $this->disabledButtonHtmlOptions['class'];
        return "function(jqXHR, settings){
            $('#{$this->searchResultsId}').fadeTo(150, 0.5);
            $('#{$searchButtonId}').attr('class', '{$disabledClass}');
            $('#{$searchButtonId}').prop('disabled', 'true');
            $('#{$searchButtonId}').val('{$this->searchProgressTitle}');
            
            var ecSearchData = {};
            $('body').trigger('{$this->collectDataEvent}', [ecSearchData]);
            
            var encodedData = JSON.stringify(ecSearchData);
            settings.data = settings.data + '&data=' + encodedData;
            
            return true;
        }";
    }
    
    /**
     * Получить js-код, выполняющийся после успешного ajax-запроса из формы поиска
     * 
     * @return string
     */
    protected function createSuccessSearchJs()
    {
        $searchButtonId    = $this->searchButtonHtmlOptions['id'];
        $searchButtonClass = $this->searchButtonHtmlOptions['class'];
        return "function(data, status){
            $('#{$this->searchResultsId}').html(data);
        
            $('#{$searchButtonId}').attr('class', '{$searchButtonClass}');
            $('#{$searchButtonId}').removeProp('disabled');
            $('#{$searchButtonId}').val('{$this->searchButtonTitle}');
            $('#{$this->searchResultsId}').fadeTo(150, 1);
        }";
    }
    
    /**
     * Отобразить кнопку "очистить"
     * @return null
     */
    protected function displayClearButton()
    {
        if ( ! $this->clearUrl )
        {
            return;
        }
        $clearUrl  = Yii::app()->createUrl($this->clearUrl);
        $refreshJs = '';
        if ( $this->refreshDataOnChange )
        {
            $refreshJs = "$('body').trigger('{$this->refreshDataEvent}');";
        }
        $clearJs = "function(data, status){
            $('body').trigger('{$this->clearSearchEvent}');
        }";
        
        $ajaxData = array(
            // @todo удалить sectionId
            'sectionId'      => $this->getSectionId(),
            'searchObjectId' => $this->getSearchObjectId(),
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
        );
        $ajaxOptions = array(
            'url'        => $clearUrl,
            'data'       => $ajaxData,
            'type'       => 'post',
            'beforeSend' => $clearJs,
            'success'    => 'function(data, status){'.$refreshJs.'}',
        );
        
        echo CHtml::ajaxButton('Очистить', $clearUrl, $ajaxOptions, $this->clearButtonHtmlOptions);
    }
    
    /**
     * Отобразить кнопку "вернуться в расширенный поиск"
     * @return null
     */
    protected function displayBackToFormButton()
    {
        if ( ! $this->backToFormUrl )
        {// не нужно ее отображать
            return;
        }
        $backToFormUrl = Yii::app()->createUrl($this->backToFormUrl);
        
        echo '<br><br>'.CHtml::link('В расширенный поиск', $backToFormUrl, $this->backToFormButtonHtmlOptions);
    }
    
    /**
     * Получить скрипт, обновляющий результаты поиска в ответ на AJAX-запрос
     * @return null
     * 
     * @todo дописать позже
     */
    protected function registerSearchResultsRefreshScript()
    {
        $ajaxOptions = $this->getAjaxSearchOptions();
        $searchJs    = CHtml::ajax($ajaxOptions);
        
        $js = "$('body').on('{$this->refreshDataEvent}', function(event){ {$searchJs} });";
        Yii::app()->clientScript->registerScript('_ecRefreshSearchResultsJS#', $js, CClientScript::POS_END);
    }
    
    /**
     * Получить id текущего раздела каталога (если отображаются фильтры) или 0 (если отображается большая форма)
     * @return int
     */
    protected function getSearchObjectId()
    {
        if ( is_object($this->searchObject) )
        {
            return $this->searchObject->id;
        }
        return 0;
    }
    
    /**
     * Получить id текущего раздела каталога (если отображаются фильтры) или 0 (если отображается большая форма)
     * @return number
     * 
     * @deprecated не используется, удалить при рефакторинге
     */
    protected function getSectionId()
    {
        return $this->getSearchObjectId();
    }
}