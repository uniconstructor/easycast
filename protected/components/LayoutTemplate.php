<?php

/**
 * Класс виджета, подгружающего и верстку и данные из базы
 */
class LayoutTemplate extends CWidget
{
    /**
     * @var string - шаблон (или фрагмент шаблона) разметки страницы
     */
    public $template = '';
    /**
     * @var array - данные подставляемые в шаблон
     */
    public $data = array();
    
    /**
     * @see parent::init()
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * @see parent::run()
     */
    public function run()
    {
        $this->getController()->renderText($this->evaluateExpression($this->template, $this->data));
    }
}

