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
     * (non-PHPdoc)
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
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('content');
    }
}