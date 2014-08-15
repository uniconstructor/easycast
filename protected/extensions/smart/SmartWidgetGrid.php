<?php

/**
 * @todo доработать до приличного состояния
 */
class SmartWidgetGrid extends CWidget
{
    /**
     * @var массив виджетов или настроек виджетов Jarvis
     */
    public $widgets = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('widgetGrid/grid');
    }
}