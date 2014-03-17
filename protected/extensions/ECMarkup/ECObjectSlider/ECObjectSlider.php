<?php

/**
 * Виджет-слайдер на базе плагина carouFredSel
 * Позволяет отображать листающийся список объектов
 */
class ECObjectSlider extends CWidget
{
    /**
     * @var array - массив объектов для вывода в слайдере: каждый элемент - это строка HTML-кода
     */
    public $objects   = array();
    /**
     * @var bool - подключать ли JS-библиотеку слайдера?
     */
    public $includeJs = true;
    /**
     * @var array - параметры для js-виджета carouFredSel
     */
    public $options   = array(
        'width'  => 1040,
        'height' => 250,
        'auto'   => false,
    );
    /**
     * @var string - id элемента из которого создается виджет
     */
    public $containerId = 'slider_news';
    /**
     * @var string - id элемента со стрелкой назад
     */
    public $prevId      = 'prev';
    /**
     * @var string - id элемента со стрелкой вперед
     */
    public $nextId      = 'next';
    
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
            Yii::app()->extensionPath . DIRECTORY_SEPARATOR .
            'ECMarkup' . DIRECTORY_SEPARATOR .
            'ECObjectSlider' . DIRECTORY_SEPARATOR .
            'assets'   . DIRECTORY_SEPARATOR);
        
        if ( $this->includeJs )
        {
            Yii::app()->clientScript->registerScriptFile($this->assetUrl.'/jquery.carouFredSel-6.2.1.js', 
                CClientScript::POS_HEAD);
        }
        
        $this->options['prev'] = '#'.$this->prevId;
        $this->options['next'] = '#'.$this->nextId;
        
        // создаем и регистрируем скрипт инициализации виджета (после установки всех параметров)
        $options = CJSON::encode($this->options);
        $js = "\$('#{$this->containerId}').carouFredSel({$options});";
        Yii::app()->clientScript->registerScript($this->id.'_init', $js, CClientScript::POS_READY);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('slider');
    }
}