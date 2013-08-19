<?php

/**
 * Заголовок письма большими буквами (например "Приглашение")
 */
class EMailHeader extends CWidget
{
    public $header = null;
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->header = trim($this->header);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->header )
        {// заголовок не задан - ничего не отображаем
            return;
        }
        $this->render('header');
    }
}