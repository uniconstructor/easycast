<?php

/**
 * Собственный класс, расширяющий возможности bootstrap-элемента popover
 * Позволяет добавлять подсказку к любому элементу, позволяет задавать ширину подсказки
 * 
 * @todo добавить проверку входных параметров
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
     * @see CWidget::init()
     */
    public function init()
    {
        if ( isset($this->htmlOptions['class']) )
        {
            $this->htmlOptions['class'] = 'popover '.$this->htmlOptions['class'];
        }else
        {
            $this->htmlOptions['class'] = 'popover';
        }
        $this->js = $this->createInitScript();
        if ( ! $this->isAjaxRequest )
        {
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
            echo '<script>'.$this->js.'</script>';
        }
    }
    
    /**
     * Получить JS для инициализации подсказки
     * @return void
     */
    protected function createInitScript()
    {
        //$loadingIcon = '<i class="icon-spinner icon-spin icon-large"></i>';
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
            $this->titleAjaxOptions['replace']   = '#'.$this->getId().'_title';
            $popoverOptions['title']   = 'js:function(){'.
                CHtml::ajax($this->titleAjaxOptions).'}';
        }
        if ( $this->contentAjaxOptions )
        {
            $progress = '<p class="lead text-center muted">Загрузка...</p><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>';
            $contentId = '#'.$this->getId().'_content';
            $this->contentAjaxOptions['replace'] = $contentId;
            $this->contentAjaxOptions['beforeSend'] = "js:function(){ $('{$contentId}').html='{}'}";
            $popoverOptions['content'] = 'js:function(){'.
                CHtml::ajax($this->contentAjaxOptions).'}';
        }
        
        // создаем полный набор параметров для активации popover-подсказки
        $popoverOptions = CJavaScript::encode($popoverOptions);
        
        return "$('{$this->triggerSelector}').popover({$popoverOptions});";
    }
}