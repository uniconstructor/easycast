<?php

/**
 * Виджет для выбора из трех состояний "да", "нет" и "не указано"
 * 
 * @todo документировать все методы и поля класса
 * @todo расширить функционал и позволить выбирать более чем из 2 вариантов
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
     * @var string - тип не нажатой кнопки (допустимы варианты кнопое из twitter bootstrap)
     * @see TbButton::type
     */
    public $defaultType = 'default';
    /**
     * @var string - тип кнопки "да"
     */
    public $onType = 'primary';
    /**
     * @var string - тип кнопки "нет"
     */
    public $offType = 'primary';
    /**
     * @var string
     */
    public $defaultValue = '';
    /**
     * @var string
     */
    public $onValue = '1';
    /**
     * @var string
     */
    public $offValue = '0';
    /**
     * @var string
     */
    public $onId;
    /**
     * @var string
     */
    public $offId;
    /**
     * @var string
     */
    public $afterOn;
    /**
     * @var string
     */
    public $afterOff;
    /**
     * @var CFormModel|CActiveRecord
     */
    public $model;
    /**
     * @var string
     */
    public $attribute;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $value;
    /**
     * @var array - параметры html для скрытого input-тега
     */
    public $hiddenHtmlOptions = array();
    /**
     * @var string - тип отображения виджета
     *               default: просто bootstrap-кнопка, никакого форматирования выравнивания и подписей
     */
    public $displayMode = 'default';
    
    /**
     * @var string - id скрытого тега
     */
    protected $hiddenId;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
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
        {
            $this->value = CHtml::value($this->model, $this->attribute);
        }
        
        if ( ! $this->onId )
        {
            $this->onId = $this->id.'_on';
        }
        if ( ! $this->offId )
        {
            $this->offId = $this->id.'_off';
        }
        
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
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->hasModel() )
        {
            echo CHtml::activeHiddenField($this->model, $this->attribute, $this->hiddenHtmlOptions);
        }else
        {
            echo CHtml::hiddenField($this->name, $this->value, $this->hiddenHtmlOptions);
        }
        
        $this->widget('bootstrap.widgets.TbButtonGroup',
            array(
                'type'   => $this->defaultType,
                'toggle' => 'radio',
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
     * 
     * @param string $type
     * @return void
     */
    protected function createToggleJs($type)
    {
        $js = '';
        if ( $type == 'on' )
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
        
        $js .= "$('#{$disableId}').removeClass('btn-{$disableType}');";
        $js .= "$('#{$enableId}').addClass('btn-{$enableType}');";
        $js .= "$('#{$this->hiddenId}').val('{$newValue}');";
        $js .= $finalJs;
        
        return $js;
    }
    
    /**
     * 
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
        {
            $options['type'] = $this->onType;
        }
        return $options;
    }
    
    /**
     * 
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
        {
            $options['type'] = $this->offType;
        }
        return $options;
    }
}