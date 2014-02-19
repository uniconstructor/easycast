<?php

/**
 * Виджет отображающий всплывющее окно с текстом обращения Коли
 * "Я гарантирую это!" :)))
 * 
 * @todo перенести modal.css в набор стилей сайта чтобы использовать его для других modal-окон
 */
class ECIGuaranteeIt extends CWidget
{
    /**
     * @var string
     */
    public $modalId = 'IGuaranteeItModal';
    
    /**
     * @var string
     */
    protected $assetUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        
        $this->assetUrl = Yii::app()->assetManager->publish(
            Yii::getPathOfAlias('ext.ECMarkup.ECIGuaranteeIt.assets') . DIRECTORY_SEPARATOR);
        // подключаем специальные стили для bootstrap modal-окон, которые позволяют 
        // делать окна широкими
        //Yii::app()->clientScript->registerCssFile($this->assetUrl.'/modal.css');
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('seriosly');
    }
}