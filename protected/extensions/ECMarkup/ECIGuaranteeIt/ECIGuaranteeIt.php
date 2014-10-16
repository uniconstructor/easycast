<?php

/**
 * Виджет отображающий всплывющее окно с текстом обращения
 * 
 * @todo перенести modal.css в набор стилей сайта чтобы использовать его для других modal-окон
 *       или удалить его совсем если обнаружится что в bootstrap3 он не нужен
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
        // задаем путь к скриптам, стилям и изображениям виджета
        $assetPath = Yii::getPathOfAlias('ext.ECMarkup.ECIGuaranteeIt.assets').DIRECTORY_SEPARATOR;
        $this->assetUrl = Yii::app()->assetManager->publish($assetPath);
        // подключаем специальные стили для bootstrap modal-окон, 
        // которые позволяют делать окна широкими
        //Yii::app()->clientScript->registerCssFile($this->assetUrl.'/modal.css');
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // получаем и выводим содержимое виджета как строку, чтобы избежать проблем
        // с вызовами beginWdidget()/endWidget() внутри представления (view)
        echo $this->render('seriosly', null, true);
    }
}