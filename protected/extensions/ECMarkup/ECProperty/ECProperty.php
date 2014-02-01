<?php

/**
 * Виджет для отображения одного свойства объекта
 * Прямоугольный блок с полоской слева
 */
class ECProperty extends CWidget
{
    /**
     * @var string - пояснение для значения
     */
    public $label;
    /**
     * @var string - само значение
     */
    public $value;
    /**
     * @var string - краткое пояснение после значения
     */
    public $affix;
    /**
     * @var string - краткое пояснение под значением
     */
    public $hint;
    /**
     * @var string - тип виджета (влияет на цвет)
     *              info
     *              primary
     *              default
     *              warning
     *              danger
     *              inverse
     */
    public $type = 'info';
    /**
     * @var bool - "выключает" виджет: делает его серым
     *              (равнозначно type='default', параметр сделан для удобства)
     */
    public $muted = false;
    /**
     * @var array - параметры контейнера виджета
     */
    public $htmlOptions = array();
    /**
     * @var string - контейнер внутри которого будет размещено значение
     */
    public $valueTag = 'p';
    /**
     * @var string - htmlOptions для контейнера со значением
     */
    public $valueOptions = array(
        'class' => 'lead',
    );
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( $this->muted )
        {
            $this->type  = 'default';
            $this->affix = '';
            $this->valueOptions['class'] .= ' muted';
        }
        $defaults = array(
            'class' => 'ec-property-container drop-shadow bottom ec-gradient-light-radial ec-left-dash-'.$this->type,
        );
        $this->htmlOptions = CMap::mergeArray($defaults, $this->htmlOptions);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('property');
    }
}