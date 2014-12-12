<?php

/**
 * Настраиваемый виджет с подгрузкой содержимого по AJAX
 * 
 * Поля, доступ к которым осуществляется через сеттеры:
 * @property array $htmlOptions
 * @property array $headerOptions
 * @property array $contentOptions
 * @property array $editBoxOptions
 * @property array $bodyOptions
 * @property array $footerOptions
 */
class JarvisWidget extends CWidget
{
    /**
     * @var bool - Prevent a widget from being sortable (can only be used with the value 'false').
     */
    public $sortable         = false;
    /**
     * 
     * @var string - Use an icon in the widgets header 
     *               Example: 'fa fa-trash'
     */
    public $icon;
    /**
     * @var bool - Prevent a widget from having a toggle button (can only be used with the value 'false').
     */
    public $toggleButton     = false;
    /**
     * @var bool - Prevent a widget from having a delete button (can only be used with the value 'false').
     */
    public $deleteButton     = false;
    /**
     * @var bool - Prevent a widget from having a edit button (can only be used with the value 'false').
     */
    public $editButton       = false;
    /**
     * @var bool - Prevent a widget from having a fullscreen button (can only be used with the value 'false').
     */
    public $fullscreenButton = false;
    /**
     * @var string - The url that is loaded with ajax.
     *               example /ajax/widgetContent
     */
    public $load;
    /**
     * @var int - Seconds to refresh an ajax file
     */
    public $refresh;
    /**
     * @var bool - Prevent a widget from having a edit button (can only be used with the value 'false').
     */
    public $refreshButton = false;
    /**
     * @var int - Seconds to refresh an ajax file
     */
    public $hidden        = false;
    /**
     * @var bool - Prevent a widget from having a color button (can only be used with the value 'false').
     */
    public $colorButton   = false;
    /**
     * @var bool - Collapse a widget upon load (can only be used with the value 'true'). 
     *             This will allways be collapsed every page load.
     */
    public $collapsed     = false;
    /**
     * @var bool - You can exclude grids from being a sortable/droppable area, 
     *             this means that all widgets in this area will work, but cant be 
     *             sortable/droppable and that all other widgets cant be dropped in this area. 
     *             Add this attribute (can only be used with the value 'false') to a grid element.
     */
    public $grid          = true;
    /**
     * @var string - заголовок виджета (text/html)
     *               Пример '<strong>Widget</strong> <i>Colors</i>'
     */
    public $header;
    /**
     * @var string - основное содержимое виджета (text/html)
     *               (будет заменено если используется загрузка через AJAX)
     */
    public $body;
    /**
     * @var string - содержимое подвала (footer) виджета (text/html)
     *               (будет заменено если используется загрузка через AJAX)
     */
    public $footer;
    /**
     * @var string - содержимое формы редактирования/настройки виджета (html)
     */
    public $editBox;
    /**
     * @var array - массив дополнительных элементов управления виджетом (в заголовке)
     *              Каждый элемент массива - строка:
     *              array(
     *                  '<input type="text" name="example1">',
     *                  ...
     *              )
     */
    public $headerTools = array();
    /**
     * @var array - массив дополнительных элементов управления виджетом (перед содержимым)
     *              Каждый элемент массива - строка:
     *              array(
     *                  '<input type="text" name="example1">',
     *                  ...
     *              )
     */
    public $bodyTools   = array();
    /**
     * @var bool - Removes all padding inside widget body if false
     */
    public $bodyPadding = true;
    /**
     * @var bool - Converts a widget to a well if true
     */
    public $well        = false;
    /**
     * @var bool - инициализировать виджет используя html5-параметры (data-...) [true]
     *             или использовать JS-инициализацию [false]
     */
    public $useDataAttributes = true;
    
    /**
     * @var array - параметры основного тега виджета
     */
    protected $htmlOptions = array(
        'class' => 'jarviswidget',
    );
    /**
     * @var array - параметры заголовка виджета (тег header)
    */
    protected $headerOptions = array();
    /**
     * @var array - параметры содержимого виджета
    */
    protected $contentOptions = array();
    /**
     * @var array - параметры контейнера для формы редактирования виджета
     *              (только если для этого виджета включено редактирование [editbutton])
     *              находится внутри .content
    */
    protected $editBoxOptions = array(
        'class' => 'jarviswidget-editbox',
    );
    /**
     * @var array - параметры для основного содержимого виджета
     *              находится внутри .content
    */
    protected $bodyOptions = array(
        'class' => 'widget-body',
    );
    /**
     * @var array - параметры для для подвала виджета (footer)
     *              находится внутри .body
    */
    protected $footerOptions = array(
        'class' => 'widget-footer',
    );
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! isset($this->htmlOptions['id']) )
        {
            $this->htmlOptions['id'] = $this->id;
        }
        if ( ! $this->bodyPadding )
        {
            $this->bodyOptions['class'] .= ' no-padding';
        }
        if ( $this->well )
        {
            $this->htmlOptions['class'] .= ' well';
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('widget');
    }
    
    /**
     * Сеттер для параметра htmlOptions
     * 
     * @param  array $options
     * @return void
     */
    public function setHtmlOptions(array $options)
    {
        $this->htmlOptions = $this->safeMergeOptions('htmlOptions', $options);
    }
    
    /**
     * Сеттер для параметра headerOptions
     * 
     * @param  array $options
     * @return void
     */
    public function setHeaderOptions(array $options)
    {
        $this->headerOptions = $this->safeMergeOptions('headerOptions', $options);
    }
    
    /**
     * Сеттер для параметра contentOptions
     * 
     * @param  array $options
     * @return void
     */
    public function setContentOptions(array $options)
    {
        $this->contentOptions = $this->safeMergeOptions('contentOptions', $options);
    }
    
    /**
     * Сеттер для параметра editBoxOptions
     * 
     * @param  array $options
     * @return void
     */
    public function setEditBoxOptions(array $options)
    {
        $this->editBoxOptions = $this->safeMergeOptions('editBoxOptions', $options);
    }
    
    /**
     * Сеттер для параметра bodyOptions
     * 
     * @param  array $options
     * @return void
     */
    public function setBodyOptions(array $options)
    {
        $this->bodyOptions = $this->safeMergeOptions('bodyOptions', $options);
    }
    
    /**
     * Сеттер для параметра footerOptions
     * 
     * @param  array $options
     * @return void
     */
    public function setFooterOptions(array $options)
    {
        $this->footerOptions = $this->safeMergeOptions('footerOptions', $options);
    }
    
    /**
     * Метод для слияния массивов настроек который позволяет сохранить 
     * изначальное значение элементов массива в классе
     * (используется в сеттерах)
     * 
     * @param  string $field
     * @param  array  $options - сохраняемый сеттером массив
     * @param  array  $keys
     * @return array
     */
    protected function safeMergeOptions($field, array $options, array $keys=array('class'))
    {
        foreach ( $keys as $key )
        {
            if ( isset($options[$key]) AND isset($this->$field[$key]) )
            {
                $this->$field[$key] .= ' '.$options[$key];
                unset($options[$key]);
            }
        }
        return CMap::mergeArray($this->$field, $options);
    }
}