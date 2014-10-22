<?php

/**
 * Контейнер для блоков (абзацев) текста письма
 * Получает массив настроек для каждого блока и в цикле создает их
 */
class EMailContent extends CWidget
{
    /**
     * @var array - массив, каждый элемент которого является массивом настроек для виджета EMailSegment
     *             Структура массива:
     *             type
     *             header
     *             text
     *             imageLink
     *             columns (используется только при верстке в несколько колонок)
     *                 type
     *                 header
     *                 text
     *                 imageLink
     */
    public $segments = array();
    /**
     * @var int - делать ли отступы (30px) для всех блоков письма? 
     */
    public $padding = 30;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( empty($this->segments) )
        {
            throw new InvalidArgumentException('Email content is empty!');
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->padding )
        {
            $this->render('content');
        }else
        {
            $this->render('contentNoPadding');
        }
    }
}