<?php

/**
 * Универсальный виджет для вывода произвольного фрагмента разметки
 */
class ArTemplateWidget extends CWidget
{
    /**
     * @var ArTemplate|CustomActiveRecord
     */
    public $arTemplate;
    /**
     * @var array
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
        $this->evaluateExpression($this->arTemplate->content, $this->data);
    }
}