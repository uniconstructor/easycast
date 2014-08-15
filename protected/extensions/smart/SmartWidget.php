<?php

/**
 * 
 */
class SmartWidget extends CWidget
{
    /**
     * @var array
     */
    public $articleOptions = array(
        'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
    );
    public $colorbutton = false;
    
    public $editbutton = false;
    
    public $togglebutton = false;
    
    public $deletebutton = false;
    
    public $fullscreenbutton = false;
    
    public $custombutton = false;
    
    public $collapsed = true;
    
    public $sortable = false;
    
    public $htmlOptions = array(
        'class' => 'jarviswidget',
    );
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $content;
        
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! isset($this->htmlOptions['id']) )
        {
            $this->htmlOptions['id'] = $this->id;
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('widgetGrid/widget');
    }
}