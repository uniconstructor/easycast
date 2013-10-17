<?php

/**
 * Простой виджет для вывода alert-блока со стилями Twitter Bootstrap
 * Стандартный TbAlert нам не подходит, потому что он предназначен только для вывода flash-сообщений
 */
class ECAlert extends CWidget
{
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO    = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR   = 'error';
    const TYPE_DANGER  = 'danger'; // то же что и error (для совместимости с названиями стилей кнопок)
    
    /**
     * @var string - заголовок сообщения (необязательно)
     */
    public $header = '';
    /**
     * @var string - само сообщение. Разрешен HTML.
     */
    public $message = '';
    /**
     * @var string - текст (или значок) при нажатии на который происходит закрытие сообщения
     */
    public $closeText = '&times;';
    /**
     * @var string - тип сообщения
     */
    public $type = '';
    /**
     * @var bool - разрешить ли закрывать (убирать) сообщение?
     */
    public $allowClose = true;
    /**
     * @var bool - выводить широкий блок с сообщением
     */
    public $block = true;
    /**
     * @var array - параметры главного div-тега с сообщением
     */
    public $htmlOptions = array();
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $allowedTypes = array(self::TYPE_SUCCESS, self::TYPE_INFO, self::TYPE_WARNING, self::TYPE_ERROR, self::TYPE_DANGER);
        if ( ! isset($this->htmlOptions['id']) )
        {
            $this->htmlOptions['id'] = $this->getId();
        }
        if ( $this->type AND ! in_array($this->type, $allowedTypes) )
        {
            throw new CException('Invalid alert type');
        }
        $classes = $this->getAlertClasses($type);
        if ( isset($this->htmlOptions['class']) )
        {
            $this->htmlOptions['class'] = $classes.$this->htmlOptions['class'];
        }else
        {
            $this->htmlOptions['class'] = $classes;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo CHtml::openTag('div', $this->htmlOptions);
        if ( $this->allowClose )
        {// отображаем кнопку для скрытия сообщения
            echo CHtml::openTag('button', array(
                'id'           => $this->htmlOptions['id'].'_close_alert_button',
                'class'        => 'close',
                'type'         => 'button',
                'data-dismiss' => 'alert',
            ));
            echo $this->closeText;
            echo CHtml::closeTag('button');
        }
        if ( $this->header )
        {// отображаем заголовок
            echo CHtml::openTag('h4', array('class' => 'alert-heading'));
            echo $this->header;
            echo CHtml::closeTag('h4');
        }
        // выводим само сообщение
        echo $this->message;
        echo CHtml::closeTag('div');
    }
    
    /**
     * Получить CSS-класс alert-сообщения по его типу
     * @param string $type
     * @return string
     */
    protected function getAlertClasses($type)
    {
        $styles = 'alert ';
        if ( $this->block )
        {
            $styles .= 'alert-block ';
        }
        switch ( $type )
        {// вообще Стив Макконелл не рекомендует использовать switch-конструкции без break
            // но здесь это действительно удобно, зацените:
            case self::TYPE_SUCCESS:
            case self::TYPE_INFO:
            case self::TYPE_WARNING: $styles .= "alert-{$type} ";
            case self::TYPE_ERROR:
            case self::TYPE_DANGER:  $styles .= "alert-danger ";
        }
        return $styles;
    }
}