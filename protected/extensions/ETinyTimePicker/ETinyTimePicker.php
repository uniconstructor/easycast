<?php

/**
 * Маленький виджет для выбора количества минут
 */
class ETinyTimePicker extends CInputWidget
{
    public $defaultTime = 1800; // 30 min
    
    public $maxMinutes  = 210;
    /**
     * @var int - интервал между значениями в минутах 
     */
    public $step        = 10;
    
    public $showEmptyOption = true;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo CHtml::activeDropDownList($this->model, $this->attribute, $this->getVariants(), $this->htmlOptions);
    }
    
    protected function getVariants()
    {
        $variants = array();
        $model      = $this->model;
        $attribute  = $this->attribute;
        $modelValue = $model->$attribute;
        
        if ( $this->showEmptyOption )
        {
            $variants[0] = 'Не задано';
        }
        for ( $interval = $this->step; $interval <= $this->maxMinutes; $interval += $this->step )
        {
            $intervalInSeconds = $interval * 60;
            if ( $intervalInSeconds == $this->defaultTime AND ! $modelValue )
            {
                $option = array();
                //$option['label'] = ;
                //$option['value'] = ;
                $option['selected'] = true;
                $this->htmlOptions['options'][$intervalInSeconds] = $option;
            }
            $variants[$intervalInSeconds] = 'за '.$interval.' мин';
        }
        
        return $variants;
    }
}