<?php

/**
 * Форма заявки на активацию онлайн-кастинга
 * @deprecated не пригодилось - удалить при рефакторинге
 */
class CastingActivationRequest extends CWidget
{
    /**
     * @var Project - проект онлайн-кастинга, который должен быть активирован
     */
    public $project;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('form');
    }
}