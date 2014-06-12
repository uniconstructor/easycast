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
     * @var string - режим отображения фильтров:
     *               filter - фильтр в разделе каталога 
     *                        (набор фильтров берется из раздела каталога)
     *               form   - большая форма поиска 
     *                        (набор фильтров берется из первого (корневого) раздела каталога "все")
     */
    public $mode       = 'form';
    /**
     * @var string - источник данных для формы (откуда будут взяты значения по умолчанию)
     *               Возможные значения:
     *               'session' - данные берутся из сессии (используется во всех формах поиска)
     *               'db' - данные берутся из базы (используется при сохранении критериев вакансии и т. п.)
     */
    public $dataSource = 'session';
    /**
     * @var string - url по которому происходит переход после поиска
     *               Если этот параметр не задан - то перенаправления не происходит
     */
    public $redirectUrl    = '';
    /**
     * @var string - url по которому запрашивается количество найденных (подходящих) участников
     *               Если этот параметр не задан - то количество участников отображаться не будет
     *               Ответ пришедший из этого url должен возвращать единственное целое число
     */
    public $countUrl       = '';
    /**
     * @var string - название jQuery события, посылаемого для подсчета обновления количества подходящих участников
     */
    public $countDataEvent = 'countData';
    /**
     * @var bool - обновлять результаты поиска при каждом изменении критериев поиска
     */
    public $refreshDataOnChange = false;
    /**
     * @var bool - обновлять количество найденых участников при каждом изменении критериев поиска
     */
    public $countDataOnChange   = true;
    /**
     * @var string - id тега внутри которого содержится число подходящих участников
     */
    public $countResultsId = 'count_results';
    /**
     * @var string - расположение блока со счетчиком
     *               top    - над всеми полями формы
     *               bottom - после полей формы, над кнопками
     */
    public $countResultPosition  = 'top';
    /**
     * @var bool - отображать ли количество подходящих участников
     */
    public $displayCount = true;
    /**
     * @var array
     * @todo добавить возможность установки id через этот массив
     * @todo ставить класс hide в зависимости от countDisplay
     */
    public $countContainerHtmlOptions = array(
        'class' => 'well hide text-center',
    );
    /**
     * @var array - массив настроек для каждого фильтра
     */
    public $filterOptions = array(
        'iconlist' => array(
            'buttonAlignment' => 'vertical',
            'clearByEvent'    => true,
        ),
    );
    /**
     * @var array - распределение фильтров по колонкам формы поиска
     */
    public $columnFilters = array(
        'sections' => array('iconlist'),
        'base'     => array('status', 'gender', 'age', /*'email',*/ 'salary', 'height', 
            'weight', 'body', 'system', 'name',
        ),
        'looks'    => array('looktype', 'physiquetype', 'haircolor', 'hairlength', 
            'eyecolor', 'shoessize', 'wearsize', 'titsize', 'hastatoo', 'addchar',
        ),
        'skills'   => array('dancer', 'voicetimbre', 'instrument', 'sporttype', 
            'extremaltype', 'language', 'driver', 'striptease',
        ),
    );
    /**
     * @var array - параметры отображения для кнопки "Найти"
     */
    public $searchButtonHtmlOptions = array(
        'class' => 'btn btn-success btn-large',
        'id'    => 'search_button',
    );
    /**
     * @var array - параметры отображения для кнопки "Очистить"
     */
    public $clearButtonHtmlOptions = array(
        'class' => 'btn btn-primary btn-large',
        'id'    => 'clear_search',
    );
    /**
     * @var array - параметры отображения для кнопки "Очистить"
     */
    public $disabledButtonHtmlOptions = array(
        'class' => 'btn btn-disabled btn-large',
    );
    
    /**
     * @var string
     */
    protected $_assetUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        // режим отображения: широкая форма поиска в 4 колонки
        $this->mode = 'form';
        
        if ( ! isset($this->countContainerHtmlOptions['id']) )
        {
            $this->countContainerHtmlOptions['id'] = $this->countResultsId.'_container';
        }
        // регистрируем скрипт счетчика - он автоматически обновляет количество подходящих участников
        // при каждом изменении критериев поиска
        $this->createCountRefreshJs();
    }
    
    /**
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
            $options = $this->getDisplayFilterOptions($filter);
            // путь классу виджета-критерия поиска
            $path = 'catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass;
            // отображаем фильтр поиска
            echo '<li style="display:block;">';
            $this->widget($path, $options);
            echo '</li>';
        }
    }
    
    /**
     * Определить, включен ли фильтр в прикрепленном объекте поиска
     * @param string $name - короткое название фильтра поиска
     * @return boolean
     * 
     * @todo дописать проверку "включен ли фильтр" - пока здесь только заглушка
     */
    protected function filterEnabled($name)
    {
        if ( Yii::app()->user->checkAccess('Admin') )
        {
            return true;
        }
        return true;
        //if ( $filter = $this->getFilterByShortName($name) )
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
     * @return string
     * 
     * @todo сделать возможность настройки, что обновлять: результат счетчика, 
     *       результат поиска или и то и другое
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
            //$('#{$this->searchResultsId}').html(data);
            $('#{$this->countResultsId}').html(data);
            
            $('#{$this->searchButtonHtmlOptions['id']}').removeProp('disabled');
            $('#{$this->searchButtonHtmlOptions['id']}').attr('class', '{$this->searchButtonHtmlOptions['class']}');
            $('#{$this->searchButtonHtmlOptions['id']}').val('{$this->searchButtonTitle}');
        }";
    }
    
    /**
     * Получить js-код для обновления счетчика найденных участников
     * @return void
     * 
     * @todo перенести в SearchFilters с названием createCountDataJs
     */
    protected function createCountRefreshJs()
    {
        if ( ! $this->countUrl )
        {
            return;
        }
        $beforeSendJs = "function(jqXHR, settings){
            $('#{$this->countResultsId}_container').removeClass('hide');
            var ecSearchData = {};
            $('body').trigger('{$this->collectDataEvent}', [ecSearchData]);
            
            var encodedData = JSON.stringify(ecSearchData);
            settings.data = settings.data + '&data=' + encodedData;
        }";
        $ajaxOptions = array(
            'url'        => Yii::app()->createUrl($this->countUrl),
            'cache'      => false,
            'type'       => 'post',
            'data'       => $this->getAjaxSearchParams(),
            'beforeSend' => $beforeSendJs,
            'update'     => '#'.$this->countResultsId,
        );
        $countJs = CHtml::ajax($ajaxOptions);
        
        $js = "$('body').on('{$this->countDataEvent}', function(event){ {$countJs} });";
        Yii::app()->clientScript->registerScript('_ecSearchCounter#', $js, CClientScript::POS_END);
    }
}