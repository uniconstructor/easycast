<?php

/**
 * Обертка для виджета jQuery DataTables
 * 
 * @see http://datatables.net/
 * 
 * @todo вынести все скрипты dataTables в assets этого плагина
 * @todo наследовать от CGridView
 */
class DataTable extends CWidget
{
    /**
     * @var string
     */
    //public $selector;
    /**
     * @var string
     */
    public $ajaxUrl;
    /**
     * @var array - конфигурация колонок таблицы
     * @see http://datatables.net/reference/option/columns
     * array(
     *     'cellType'  => 'td', // td|th
     *     'data'      => 'fieldName', // название поля в объекте data (если массив данных ассоциативный)
     *                                 // data.row.fieldName 
     *     'defaultContent' => '---',
     *     'orderable' => true, 
     *     'title'     => 'ФИО', 
     *     'type'      => 'html', // http://datatables.net/reference/option/columns.type 
     *     'visible'   => false, 
     * )
     */
    public $columns     = array();
    /**
     * @var array - данные таблицы
     */
    public $data        = array();
    /**
     * @var bool
     */
    public $hasFooter   = true;
    /**
     * @var array
     */
    public $htmlOptions = array(); 
    /**
     * @var array настройки js-плагина
     * @see http://datatables.net/reference/option/
     */
    public $options = array(
        // Features
        // Feature control DataTables' smart column width handling
        //'autoWidth' => true,
        // Feature control deferred rendering for additional speed of initialisation.
        //'deferRender' => false,
        // Feature control table information display field
        //'info' => true,
        // Use markup and classes for the table to be themed by jQuery UI ThemeRoller.
        //'jQueryUI' => false,
        // Feature control the end user's ability to change the paging display length of the table.
        //'lengthChange' => true,
        // Feature control ordering (sorting) abilities in DataTables.
        //'ordering' => true,
        // Enable or disable table pagination.
        //'paging' => true,
        // Feature control the processing indicator.
        //'processing' => false,
        // Horizontal scrolling
        //'scrollX' => true,
        // Vertical scrolling
        //'scrollY' => true,
        // Feature control search (filtering) abilities
        //'searching' => true,
        // Feature control DataTables' server-side processing mode.
        //'serverSide' => true,
        // State saving - restore table state on page reload
        //'stateSave' => true,
        // Data
        // Load data for the table's content from an Ajax source
        //'ajax' => '',
        // Data to use as the display data for the table
        //'data' => array(),
        // Options
        // Delay the loading of server-side data until second draw
        'deferLoading' => 10,
        // Initial paging start point
        //'displayStart' => 0,
        // Define the table control elements to appear on the page and in what order
        //'dom' => 'lfrtip',
        // Change the options in the page length select list.
        'lengthMenu' => array(10, 25, 50, 100),
        // Control which cell the order event handler will be applied to in a column
        //'orderCellsTop' => false,
        // Highlight the columns being ordered in the table's body
        //'orderClasses' => true,
        // Initial order (sort) to apply to the table: 
        // array(array(0, 'asc'), array(1, 'asc'))
        // array(1, 'asc')
        //'order' => array(),
        // Ordering to always be applied to the table
        //'orderFixed' => '',
        // Multiple column ordering ability control.
        //'orderMulti' => true,
        // Change the initial page length (number of rows per page)
        //'pageLength' => 10,
        // Pagination button display options
        //'pagingType' => 'full_numbers',
        // Display component renderer types
        'renderer' => 'bootstrap',
        // Retrieve an existing DataTables instance
        //'retrieve' => false,
        // Allow the table to reduce in height when a limited number of rows are shown.
        //'scrollCollapse' => false,
        // Set an initial filter in DataTables and / or filtering options.
        /*'search' => array(
            // Control case-sensitive filtering option.
            'caseInsensitive' => true,
            // Enable / disable escaping of regular expression characters in the search term.
            'regex'  => true,
            // Set an initial filtering condition on the table.
            'search' => true,
            // Enable / disable DataTables smart filtering
            'smart'  => true,
        ),*/
        // Define an initial search for individual columns.
        //'searchCols' => array(),
        // Set a throttle frequency for searching (ms)
        //'searchDelay' => 100,
        // Saved state validity duration (seconds)
        //'stateDuration' => 7200,
        // Set the zebra stripe class names for the rows in the table.
        //'stripeClasses' => array(),
        // Tab index control for keyboard navigation
        //'tabIndex' => 0,
        // Internationalisation
        'language' => array(
            'url' => '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Russian.json',
        ),
    );
    /**
     * @var array
     */
    public $selectFilters = array();
    /**
     * @var array
     */
    public $textFilters   = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->columns )
        {
            throw new CException('Не заданы колонки таблицы');
        }
        $this->htmlOptions['role'] = 'grid';
        if ( ! isset($this->htmlOptions['id']) OR ! $this->htmlOptions['id'] )
        {
            $this->htmlOptions['id'] = 'DataTable_'.$this->id;
        }
        $this->id = $this->htmlOptions['id'];
        if ( ! isset($this->htmlOptions['class']) )
        {
            $this->htmlOptions['class']  = 'table table-striped table-bordered table-hover dataTable no-footer';
        }else
        {
            $this->htmlOptions['class'] .= 'table table-striped table-bordered table-hover dataTable no-footer';
        }
        // настройка параметров таблицы
        if ( $this->ajaxUrl AND ( ! isset($this->options['ajax']) OR ! $this->options['ajax'] ) )
        {
            $this->options['ajax'] = $this->ajaxUrl;
        }
        if ( ! isset($this->options['data']) OR ! $this->options['data'] )
        {
            $this->options['data'] = $this->data;
        }
        // настройка колонок
        if ( ! isset($this->options['columns']) OR ! $this->options['columns'] )
        {
            $this->options['columns'] = $this->columns;
        }
        // регистрация скриптов оригинального плагина
        $themeUrl = Yii::app()->theme->baseUrl.'/assets/js/plugin/';
        /* @var $cs EcClientScript */
        $cs      = Yii::app()->clientScript;
        $scripts = array(
            'datatables/jquery.dataTables.min.js',
            'datatables/dataTables.colVis.min.js',
            'datatables/dataTables.tableTools.min.js',
            'datatables/dataTables.bootstrap.min.js',
            'datatable-responsive/datatables.responsive.min.js',
        );
        foreach ( $scripts as $path )
        {
            $cs->registerScriptFile($themeUrl.$path, $cs::POS_END);
        }
        // init-скрипт для таблицы
        $this->options['initComplete'] = $this->getSelectJs();
        //$tableOptions = CJSON::encode($this->options);
        $tableOptions = CJavaScript::encode($this->options);
        $initJs = "$('#{$this->htmlOptions['id']}').DataTable({$tableOptions});";
        $cs->registerScript($this->id.'_init', $initJs, $cs::POS_READY);
        // скрипт для фильтрации данных
        //$filterJs = $this->createFilterJs();
        //$cs->registerScript($this->id.'_filter', $filterJs, $cs::POS_READY);
        //$filterJs = "$('#{$this->htmlOptions['id']}').DataTable().initComplete = {$this->getSelectJs()};";
        //$cs->registerScript($this->id.'_filter', $filterJs, $cs::POS_READY);
        // очистка памяти
        $destroyJs = "$('#{$this->htmlOptions['id']}').DataTable().destroy();";
        $cs->registerScript($this->id.'_destroy', $destroyJs, $cs::POS_DESTROY);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        echo CHtml::openTag('table', $this->htmlOptions);
        // заголовок таблицы
        echo CHtml::openTag('thead');
        // фильтры поиска (если есть)
        echo CHtml::openTag('tr');
        foreach ( $this->columns as $column )
        {
            echo CHtml::tag('th', array(), $column['title']);
        }
        echo CHtml::closeTag('tr');
        echo CHtml::closeTag('thead');
        // @todo содержимое
        
        // footer
        echo CHtml::openTag('tfoot');
        echo CHtml::openTag('tr');
        foreach ( $this->columns as $column )
        {
            echo CHtml::tag('th', array(), $column['title']);
        }
        echo CHtml::closeTag('tr');
        echo CHtml::closeTag('tfoot');
        // конец таблицы
        echo CHtml::closeTag('table');
    }
    
    /**
     * 
     * 
     * @return void
     */
    protected function getSelectJs()
    {
        return "js:function () {
            var api = this.api();
            api.columns().indexes().flatten().each( function ( i ) {
                var column = api.column( i );
                var select = $('<select class=\"form-control\" style=\"width:100%;\"><option value=\"\"></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $(this).val();
                        column.search( val ? '^'+val+'\$' : '', true, false ).draw();
                    });
                column.data().unique().sort().each( function ( d, j ) {
                    //d = \$(d).text();
                    select.append('<option value=\"'+d+'\">' + d + '</option>')
                } );
            } );
        }";
    }
}