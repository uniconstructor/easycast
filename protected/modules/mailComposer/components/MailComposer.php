<?php

/**
 * Компонент для составления писем
 */
class MailComposer extends CApplicationComponent
{
    /**
     * @var array the behaviors that should be attached to this component.
     */
    public $behaviors = array(
        // функции создания писем для модуля "проекты"
        'ProjectMailsBehavior' => array(
            'class' => 'application.modules.mailComposer.behaviors.ProjectMailsBehavior',
        ),
    );
    /**
     * @var CController
     */
    public $controller;
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('application.modules.projects.models.*');
        Yii::import('application.modules.mailComposer.controllers.*');
        
        $this->controller = $this->getController();
        
        parent::init();
    }
    
    /**
     * Получить контроллер для вывода виджетов (в том числе при работе из консоли)
     * Этот метод переопределен для нормального создания писем из консоли
     * 
     * @return CController
     */
    protected function getController()
    {
        if ( $this->controller )
        {
            return $this->controller;
        }
        if ( isset(Yii::app()->controller) )
        {// запущено веб-приложение
            $this->controller = Yii::app()->controller;
        }else
        {// приложение запущено из консоли
            $this->controller = new MailController('MailController');
        }
        
        return $this->controller;
    }
    
    /**
     * Этот метод переопределен для нормального создания писем из консоли 
     * 
     * Creates a widget and executes it.
     * 
     * @param  string $className the widget class name or class in dot syntax (e.g. application.widgets.MyWidget)
     * @param  array $properties list of initial property values for the widget (Property Name => Property Value)
     * @param  boolean $captureOutput whether to capture the output of the widget. 
     *         If true, the method will capture and return the output generated by the widget. 
     *         If false, the output will be directly sent for display and the widget object will be returned. 
     *         This parameter is available since version 1.1.2.
     * @return mixed the widget instance when $captureOutput is false, or the widget output when $captureOutput is true.
     */
    public function widget($className, $properties=array(), $captureOutput=false)
    {
        return $this->controller->widget($className,$properties,$captureOutput);
    }
    
    /**
     * Создать самое простое письмо: заголовок, подзаголовок, абзац текста,
     * стандартная подпись с контактами, отписка по желанию. Все настраивается.
     *
     * @param  array $params - массив параметров для составления письма
     * @return string - html-код письма
     *
     * @todo пока только заготовка
     */
    public function createSimpleMail($header, $text, $options=array())
    {
        $defaults = $this->getMailDefaults();
        $options  = CMap::mergeArray($defaults, $options);
    
        // составляем текст письма
        $block = array(
            'header' => $header,
            'text'   => $text,
        );
        // добавляем все блоки с информацией в массив настроек для виджета EMailAssembler
        $options['segments'] = CMap::mergeArray(array($block), $options['segments']);
        
        // создаем виджет и получаем из него полный HTML-код письма
        $widgetPath = 'application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler';
        return $this->widget($widgetPath, $options, true);
    }
    
    /**
     * Получить настройки по умолчанию для составления простого письма
     * @return array
     */
    protected function getSimpleMailDefaults()
    {
        return array(
            // заголовок первого абзаца
            'header' => '',
            // первый абзац текста (только текст, html и самая простая разметка)
            'text'   => '',
            // настройки для виджета, собирающего письмо из блоков
            'assemblerOptions' => array(),
        );
    }
    
    /**
     * Получить настройки по умолчанию для виджета EMailAssembler
     *
     * @return array
     *
     * @todo брать настройки из специального плагина
     */
    public function getMailDefaults()
    {
        return array(
            'showTopServiceLinks'      => false,
            'showBottomServiceLinks'   => false,
            'showSocialButtons'        => false,
            'showContactPhone'         => true,
            'showContactEmail'         => true,
            'contactPhone'             => Yii::app()->params['userPhone'],
            'contactEmail'             => Yii::app()->params['adminEmail'],
            'mainHeader'               => '',
            'segments'                 => array(),
            'signature'                => '',
            'showFeedbackNotification' => true,
            'showPasswordNotification' => false,
            'userHasFirstAccess'       => true,
            'mainHeaderType'           => 'image',
            'contentPadding'           => 30,
        );
    }
    
    /**
     * Получить массив для создания текстового блока письма 
     * 
     * @param  string     $text - текст в блоке
     * @param  string     $header - заголовок блока (необязательно)
     * @param  array|null $editBlock - блок, который нужно дополнить
     * @return array
     * 
     * @todo удалить эту функцию при рефакторинге. Вместо нее использовать аналогичную, из класса EMailBase
     */
    public function textBlock($text, $header='', $editBlock=null)
    {
        if ( is_array($editBlock) AND ! empty($editBlock) )
        {
            $block = $editBlock;
            $block['text'] .= $text;
        }else
        {
            $block = array('text' => $text);
        }
        if ( trim($header) )
        {// заголовок всегда обновляем
            $block['header'] = $header;
        }
        return $block;
    }
}