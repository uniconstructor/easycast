<?php

/**
 * Собственный класс, расширяющий возможности bootstrap-элемента popover
 * Позволяет добавлять подсказку к любому элементу, позволяет задавать ширину подсказки
 * 
 * @todo добавить проверку входных параметров
 * @todo добавить static-метод который регистрирует обработчик события только один раз
 *      (если виджет выводится несколько раз)
 * @todo вынести регистрацию событий во внешний файл со скриптами
 * @todo сделать так, чтобы при открытии одной подсказки закрывались все остальные
 * @todo добавить иконку загрузки при ожидании данных во время AJAX-запроса
 * @todo переписать загрузку данных в виджет через события sweekit
 */
class ECPopover extends CWidget
{
    /**
     * @var bool - запрашивается ли виджет по AJAX? 
     *             true - скрипты нужно распечатать в конце виджета
     *             false - скрипты регистрируются обычным порядком
     */
    public $isAjaxRequest = true;
    /**
     * @var string - css-селектор элемента, при нажатии на который разворачивается всплывающая подсказка
     *               (не используется если заголовок загружается по AJAX)
     */
    public $triggerSelector;
    /**
     * @var string - css-селектор элемента из которого загружается содержимое подсказки
     *               (не используется если содержимое загружается по AJAX)
     */
    public $contentSelector;
    /**
     * @var array - параметры для AJAX-запроса получающего заголовок (title) подсказки
     *              (только если заголовок загружается через AJAX)
     *              см. документацию к CHtml::ajax()
     */
    public $titleAjaxOptions = array();
    /**
     * @var array - параметры для AJAX-запроса получающего основное содержимое виджета
     *              (только если содержимое загружается через AJAX)
     *              см. документацию к CHtml::ajax()
     */
    public $contentAjaxOptions = array();
    /**
     * @var array - параметры виджета-контейнера для popover элемента
     */
    public $htmlOptions = array();
    /**
     * @var string - пространство имен в котором выполняются все события подсказки
     */
    public $eventContext;
    
    public $animation = true;
    public $html      = false;
    public $placement = 'right';
    public $trigger   = 'click';
    public $title     = '';
    public $content   = '';
    public $delay     = 0;
    
    /**
     * @var string - весь JS-код виджета: будет распечатан в конце виджета если он запрашивается по AJAX
     */
    protected $js;
    /**
     * @var string - id элемента в котором содержится заголовок всплывающей подсказки
     */
    protected $titleId;
    /**
     * @var string - id элемента в котором содержится все содержимое всплывающей подсказки
     */
    protected $contentId;
    /**
     * @var string - id кнопки "закрыть"
     */
    protected $closeIconId;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        // создаем уникальные id для внутренней разметки
        $this->titleId     = $this->getId().'_title';
        $this->contentId   = $this->getId().'_content';
        $this->closeIconId = $this->getId().'_closeIcon';
        
        if ( isset($this->htmlOptions['class']) )
        {
            $this->htmlOptions['class'] = 'popover '.$this->htmlOptions['class'];
        }else
        {
            $this->htmlOptions['class'] = 'popover';
        }
        // добавляем кнопку закрытия к заголовку
        // @todo понять почему не работает из template-разметки
        $this->title .= $this->createCloseButton();
        
        // создаем скрипт подгрузки данных
        $this->createInitScript();
        if ( ! $this->isAjaxRequest )
        {// виджет подключается на страницу заранее - регистрируем скрипты как обычно
            Yii::app()->clientScript->registerScript('ecpopover_init_'.$this->getId(), $this->js);
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->isAjaxRequest )
        {
            echo CHtml::script($this->js);
        }
    }
    
    /**
     * Получить JS для инициализации подсказки
     * @return string
     */
    protected function createInitScript()
    {
        $popoverOptions = array(
            'animation' => $this->animation,
            'html'      => $this->html,
            'placement' => $this->placement,
            'trigger'   => $this->trigger,
            'delay'     => $this->delay,
            'template'  => $this->render('template', null, true),
            'title'     => $this->title,
            'content'   => $this->content,
        );
        if ( $this->titleAjaxOptions )
        {
            $this->titleAjaxOptions['replace'] = '#'.$this->titleId;
            $popoverOptions['title'] = 'js:function(){'.CHtml::ajax($this->titleAjaxOptions).'}';
        }
        if ( $this->contentAjaxOptions )
        {// @todo анимация загрузки
            //$progress = '<p class="lead text-center muted">Загрузка...</p><div class="progress progress-striped active"><div class="bar" style="width:100%;"></div></div>';
            //$this->contentAjaxOptions['beforeSend'] = "js:function(){\$('#{$this->contentId}').html('ololo');}";
            $this->contentAjaxOptions['replace'] = '#'.$this->contentId;
            $popoverOptions['content'] = 'js:function(){'.CHtml::ajax($this->contentAjaxOptions).'}';
        }
        // создаем полный набор параметров для активации popover-подсказки
        $popoverOptions = CJavaScript::encode($popoverOptions);
        
        $this->js .= "\$('{$this->triggerSelector}').popover({$popoverOptions});";
    }
    
    /**
     * Создает кнопку "закрыть подсказку"
     * @return string
     */
    protected function createCloseButton()
    {
        $url     = Sweeml::raiseEventUrl('closePopover', $this->triggerSelector);
        $closeJs = "js:function(triggerSelector){jQuery(triggerSelector).popover('hide');}\n";
        
        if ( $this->isAjaxRequest )
        {
            $this->js .= Sweeml::registerEventScript('closePopover', $closeJs);
        }else
        {
            Sweeml::registerEvent('closePopover', $closeJs);
        }
        // @todo активируем подсказку "Закрыть"
        /*$this->js .= "jQuery('body').delegate('#{$this->closeIconId}', 'click', function() {
            this.tooltip({
                title : 'Закрыть',
            });
        });\n";*/
        
        return Sweeml::link('&times;', $url, array(
            'id'    => $this->closeIconId,
            'class' => "close pull-right",
            'rel'   => 'tooltip',
            'data-toggle' => 'tooltip',
            'data-title'  => 'Закрыть',
        ));
    }
}