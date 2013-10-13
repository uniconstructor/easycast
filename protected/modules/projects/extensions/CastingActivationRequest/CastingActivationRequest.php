<?php

/**
 * Форма заявки на активацию онлайн-кастинга
 */
class CastingActivationRequest extends CWidget
{
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('form');
    }
}