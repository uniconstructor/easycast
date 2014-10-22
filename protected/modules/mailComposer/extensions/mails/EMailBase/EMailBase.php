<?php

/**
 * Базовый класс для всех шаблонов писем
 * 
 * @todo языковые строки
 */
class EMailBase extends CWidget
{
    /**
     * @var array - настройки по умолчанию для составления письма
     * @see self::getMailDefaults()
     */
    public $mailOptions = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $defaults = $this->getMailDefaults(); 
        $this->mailOptions = CMap::mergeArray($defaults, $this->mailOptions);
        
        parent::init();
    }
    
    /**
     * @see CWidget::init()
     */
    public function run()
    {
        $path = 'application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler';
        $this->widget($path, $this->mailOptions);
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
            // тип основного заголовка (изображание/текст)
            'mainHeaderType'           => 'image',
            // текст большого главного заголовка в письме
            'mainHeader'               => '',
            // фрагменты письма (стандартные блоки)
            'segments'                 => array(),
            // дополнительный текст внизу письма
            'signature'                => '',
            // напоминание внизу письма (о том как с нами связаться)
            'showFeedbackNotification' => true,
            // напоминание о том, как восстановить пароль (внизу письма)
            'showPasswordNotification' => false,
            // заходил ли участник на сайт хотя бы раз?
            'userHasFirstAccess'       => true,
        );
    }
    
    /**
     * Добавить новый фрагмент к текущему письму
     * 
     * @param array $segment - фрагмент письма
     * @return null
     */
    protected function addSegment($segment)
    {
        $this->mailOptions['segments'][] = $segment;
    }
    
    /**
     * Получить массив для создания текстового блока письма
     * 
     * @param string $text - текст в блоке
     * @param string $header - заголовок блока (необязательно)
     * @param array|nill $editBlock - блок, который нужно дополнить
     * @return array
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
    
    /**
     * Получить строку с приветствием для участника
     * 
     * @param Questionary $questionary
     * @return string
     */
    protected function createUserGreeting($questionary=null)
    {
        $name = '';
        if ( is_object($questionary) AND isset($questionary->firstname) AND trim($questionary->firstname) )
        {
            $name = $questionary->firstname;
        }
        return $this->createGreeting($name);
    }
    
    /**
     * Получить строку с приветствием для заказчика
     * 
     * @param CustomerInvite $customerInvite - приглашение заказчика
     * @return string
     *
     * @todo переместить эту функцию в виджет письма с приглашением для заказчика
     */
    protected function createCustomerGreeting($customerInvite)
    {
        $name = '';
        if ( trim($customerInvite->name) )
        {
            $name = $customerInvite->name;
        }
        return $this->createGreeting($name);
    }
    
    /**
     * Получить строку с приветствием
     * 
     * @param string $name - имя того с кем здороваемся :)
     * @return string
     */
    protected function createGreeting($name='')
    {
        if ( $name )
        {
            return $name.", здравствуйте.<br>\n<br>\n";
        }else
        {
            return "Здравствуйте.<br>\n<br>\n";
        }
    }
}