<?php

/**
 * @todo доработать до приличного состояния
 */
class SmartWidgetGrid extends CWidget
{
    /**
     * @var array массив виджетов или настроек виджетов Jarvis
     */
    public $widgets = array();
    /**
     * @var array массив колонок или подгрупп виджетов внутри сетки
     *      каждый раздел может содержать несколько виджетов, сетка
     *      верстки может содержать несколько разделов
     */
    public $sections = array();
    
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