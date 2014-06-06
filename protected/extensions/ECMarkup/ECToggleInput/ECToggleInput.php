<?php

/**
 * Виджет для выбора из трех состояний "да", "нет" и "не указано"
 * 
 * @todo расширить функционал и позволить выбирать более чем из 2 вариантов, а также checkbox-кнопки
 *       в дополнении к radio
 * @todo определять изменилось ли значение при клике. Различать случаи:
 *       - заполнение поля в первый раз (прошлое значение "пусто")
 *       - кнопка нажата, но значение не изменилось
 *       - кнопка нажата и значение изменилось
 * @todo вынести верстку во views
 * @todo выключать button-кнопки если сброшено значение в hidden-поле
 */
class ECToggleInput extends CInputWidget
{
    /**
     * @var string - текст на кнопке "да"
     */
    public $onLabel;
    /**
     * @var string - текст на кнопке "нет"
     */
    public $offLabel;
    /**
     * @var string - тип не нажатой кнопки (допустимы варианты кнопок из twitter bootstrap)
     * @see TbButton::type
     */
    public $defaultType = 'default';
    /**
     * @var string - тип кнопки "да"
     */
    public $onType   = 'primary';
    /**
     * @var string - тип кнопки "нет"
     */
    public $offType  = 'primary';
    /**
     * @var string - значение, для состояния "не заполнено"
     * @todo пока не используется
     */
    public $emptyValue;
    /**
     * @var string - значение отправляемое при выборе "да"
     */
    public $onValue  = '1';
    /**
     * @var string - значение отправляемое при выборе "нет"
     */
    public $offValue = '0';
    /**
     * @var string - id кнопки "да"
     *               Будет создано автоматически если не указано
     */
    public $onId;
    /**
     * @var string - id кнопки "нет"
     *               Будет создано автоматически если не указано
     */
    public $offId;
    /**
     * @var string - дополнительный js-код, выполняемый после нажатия на кнопку "да"
     *               рекомендуется использовать события jQuery
     */
    public $afterOn;
    /**
     * @var string - дополнительный js-код, выполняемый после нажатия на кнопку "нет"
     *               рекомендуется использовать события jQuery
     */
    public $afterOff;
    /**
     * @var CFormModel|CActiveRecord
     */
    public $model;
    /**
     * @var string - поле
     */
    public $attribute;
    /**
     * @var string - поле name для input-поля (используется если не указаны model и attribute)
     */
    public $name;
    /**
     * @var string - изначальное значение поля (используется если не указаны model и attribute)
     */
    public $value;
    /**
     * @var array - параметры html для скрытого input-тега
     */
    public $hiddenHtmlOptions = array();
    
    /**
     * @var string - id скрытого input-тега
     */
    protected $hiddenId;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        // определяем имя для input-поля и id виджета
        $this->resolveNameID();
        if ( ! $this->onLabel )
        {
            $this->onLabel = Yii::t('coreMessages', 'yes');
        }
        if ( ! $this->offLabel )
        {
            $this->offLabel = Yii::t('coreMessages', 'no');
        }
        
        if ( $this->hasModel() )
        {// получаем сохраненное значение из модели (или определяем что поле пока не заполнено)
            $this->value = CHtml::value($this->model, $this->attribute);
        }
        // определяем id для главных элементов виджета
        if ( ! $this->onId )
        {
            $this->onId = $this->id.'_on';
        }
        if ( ! $this->offId )
        {
            $this->offId = $this->id.'_off';
        }
        // определяем id для скрытого элемента, в котором хранится значение
        if ( isset($this->hiddenHtmlOptions['id']) )
        {
            $this->hiddenId = $this->hiddenHtmlOptions['id'];
        }else
        {
            if ( $this->hasModel() )
            {
                $this->hiddenId = CHtml::activeId($this->model, $this->attribute);
            }else
            {
                $this->hiddenId = CHtml::getIdByName($this->name);
            }
            $this->hiddenHtmlOptions['id'] = $this->hiddenId;
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // скрытое поле с выбранным значением
        if ( $this->hasModel() )
        {
            echo CHtml::activeHiddenField($this->model, $this->attribute, $this->hiddenHtmlOptions);
        }else
        {
            echo CHtml::hiddenField($this->name, $this->value, $this->hiddenHtmlOptions);
        }
        // основная часть виджета: все кнопки одной группой
        $this->widget('bootstrap.widgets.TbButtonGroup', array(
                'type'    => $this->defaultType,
                'toggle'  => 'radio',
                'buttons' => array(
                    // кнопка включения
                    $this->getOnButtonOptions(),
                    // кнопка выключения
                    $this->getOffButtonOptions(),
                ),
            )
        );
    }
    
    /**
     * Создать JS-код, выполняемый при нажатии на кнопку
     * @param string $type - тип кнопки для которой создается скрипт
     * @return string
     */
    protected function createToggleJs($type)
    {
        $js = '';
        if ( $type === 'on' )
        {
            $enableId    = $this->onId;
            $enableType  = $this->onType;
            $newValue    = $this->onValue;
            $disableId   = $this->offId;
            $disableType = $this->offType;
            $finalJs     = $this->afterOn;
        }else
        {
            $enableId    = $this->offId;
            $enableType  = $this->offType;
            $newValue    = $this->offValue;
            $disableId   = $this->onId;
            $disableType = $this->onType;
            $finalJs     = $this->afterOff;
        }
        
        // меняем css кнопок в зависимости от того что было нажато
        $js .= "$('#{$disableId}').removeClass('btn-{$disableType} active');";
        $js .= "$('#{$enableId}').addClass('btn-{$enableType} active');";
        // меняем значение в скрытом поле
        $js .= "$('#{$this->hiddenId}').val('{$newValue}');";
        // дополнительный js, выполняемый после нажатия на кнопку
        $js .= $finalJs;
        
        return $js;
    }
    
    /**
     * Получить параметры для кнопки "да"
     * @return array
     */
    protected function getOnButtonOptions()
    {
        $options = array(
            'label'       => $this->onLabel,
            'buttonType'  => 'button',
            'htmlOptions' => array(
                'id' => $this->onId,
                'onclick' => $this->createToggleJs('on'),
            ),
        );
        if ( $this->value == $this->onValue )
        {// выделяем кнопку, если она нажата
            $options['type'] = $this->onType;
        }
        return $options;
    }
    
    /**
     * Получить параметры для кнопки "нет"
     * @return array
     */
    protected function getOffButtonOptions()
    {
        $options = array(
            'label'       => $this->offLabel,
            'buttonType'  => 'button',
            'htmlOptions' => array(
                'id' => $this->offId,
                'onclick' => $this->createToggleJs('off'),
            ),
        );
        if ( $this->value == $this->offValue )
        {// выделяем кнопку, если она нажата
            $options['type'] = $this->offType;
        }
        return $options;
    }
}