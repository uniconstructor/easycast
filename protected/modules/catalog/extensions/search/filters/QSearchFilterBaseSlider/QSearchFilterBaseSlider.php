<?php

/**
 * Базовый класс для всех виджетов фильтров, состоящих из одного слайдера
 * @todo языковые строки
 */
class QSearchFilterBaseSlider extends QSearchFilterBase
{
    /**
     * Получить минимальное значение слайдера
     * @return int
     */
    protected function getMinValue()
    {
        throw new CHttpException('500', 'getMinValue() должен быть наследован');
    }
    
    /**
     * Получить максимальное значение слайдера
     * @return int
     */
    protected function getMaxValue()
    {
        throw new CHttpException('500', 'getMaxValue() должен быть наследован');
    }
    
    /**
     * Получить шаг слайдера (цена деления)
     * @return number
     */
    protected function getStepValue()
    {
        return 1;
    }
    
    /**
     * Получить текст, который отображается, когда в слайдере ничего не выбрано
     * @return string
     */
    protected function getNotSetPlaceholder()
    {
        return $this->getTitle();
    }
    
    /**
     * Получить значение в текстовом поле над слайдером сразу же при загрузке страницы
     * @param int $min
     * @param int $max
     * @param string $placeholder - текст отображаемый в окошке слайдера, если ничего не выбрано
     * @return string
     */
    protected function getDefaultSliderInfo($min, $max, $placeholder=null)
    {
        $minLimit = $this->getMinValue();
        $maxLimit = $this->getMaxValue();
        
        if ( $min == $minLimit AND $max == $maxLimit )
        {// не задан ни один параметр - выводим заглушку
            if ( $placeholder )
            {
                return $placeholder;
            }else
            {
                return $this->getNotSetPlaceholder();
            }
        }
        if ( $min != $minLimit AND $max == $maxLimit )
        {// указана только нижняя граница, выводим "от хх"
            return Yii::t('coreMessages', 'from').' '.$min;
        }
        if ( $min == $minLimit AND $max != $maxLimit )
        {// указана только верхняя граница, выводим "до хх"
            return Yii::t('coreMessages', 'to').' '.$max;
        }
        if ( $min != $minLimit AND $max != $maxLimit )
        {// указаны обе границы, выводим "от хх до хх"
            return Yii::t('coreMessages', 'from').' '.$min.' '.Yii::t('coreMessages', 'to').' '.$max;
        }
    }
    
    /**
     * получить все html-содержимое виджета фрагмента формы поиска
     * (функция заменяет run() для обычных виджетов)
     *
     * @return null
     */
    protected function getContent()
    {
        $content = '';
    
        $shortname = $this->filter->shortname;
        // задаем значения по умолчанию
        $values = array(
            0 => $this->getMinValue(),
            1 => $this->getMaxValue(),
        );
        if ( $defaults = $this->loadLastSearchParams() )
        {
            if ( isset($defaults['min'.$shortname]) )
            {
                $values[0] = $defaults['min'.$shortname];
            }
            if ( isset($defaults['max'.$shortname]) )
            {
                $values[1] = $defaults['max'.$shortname];
            }
        }
        
        // Выводим окошко с выбранными результатами
        $content .= CHtml::textField(null, $this->getDefaultSliderInfo($values[0], $values[1]), array(
            'id'       => 'search_'.$shortname.'_slider_info',
            'class'    => 'ec-search-filter-slider-info',
            'disabled' => 'disabled',
        ));
        
        // выводим сам виджет со слайдером
        $content .= $this->widget('zii.widgets.jui.CJuiSlider',array(
            // additional javascript options for the slider plugin
            'options' => array(
                'min'    => $this->getMinValue(),
                'max'    => $this->getMaxValue(),
                'step'   => $this->getStepValue(),
                'range'  => true,
                'values' => $values,
                'slide'  => 'js:function( event, ui ) {'.$this->getSlideJs('search_'.$shortname.'_slider_info').'}',
            ),
            'htmlOptions'=>array(
                'name' => $this->getFullInputName($shortname),
            ),
        ), true);
    
    
        return $content;
    }
    
    /**
     * Получить JS-код, который реагирует на событие собирает все данные из фрагмента формы в JSON-массив
     * (для отправки по AJAX, чтобы тут же обновлять данные поиска в сессии или динамически обновлять содержимое поиска)
     * (этот метод - индивидуальный для каждого фильтра)
     *
     * @return string
     */
    protected function createCollectFilterDataJs()
    {
        $shortname = $this->filter->shortname;
        $selector = 'div[name="'.$this->getFullInputName($this->filter->shortname).'"]';
        $js = "function {$this->collectDataJsName}() {
            var data = {};
            var sliderObj = $('{$selector}');
            if ( sliderObj.slider( 'values', 0 ) != ".$this->getMinValue()." )
            {
                data.min{$shortname} = $('{$selector}').slider('values', 0 );
            }
            if ( sliderObj.slider( 'values', 1 ) != ".$this->getMaxValue()." )
            {
                data.max{$shortname} = $('{$selector}').slider('values', 1 )
            }
            
            return data;
        };";
    
        return $js;
    }
    
    /**
     * Получить js-код для очистки выбранных пользователем значений в фрагменте формы
     * (Этот JS очищает только данные на стороне клиента. Код уникальный для каждого элемента)
     *
     * @return string
     */
    protected function createClearFormDataJs()
    {
        $js = '';
        $selector = 'div[name="'.$this->getFullInputName($this->filter->shortname).'"]';
        $js .= "$('{$selector}').slider( 'option', 'values', [ ".$this->getMinValue().", ".$this->getMaxValue()." ] );";
        $js .= '$("#search_'.$this->filter->shortname.'_slider_info").val("'.$this->getNotSetPlaceholder().'");';
        return $js;
    }
    
    /**
     * JS-код, писоединяющий к каждому input-элементу функцию, которая при изменении значения
     * определяет, нужно ли изменить внешний вид виджета или отправить AJAX для изменения данных
     * и производит эти операции если надо
     *
     * @return string
     */
    protected function createAttachInputToggleJs()
    {
        $selector = 'div[name="'.$this->getFullInputName($this->filter->shortname).'"]';
        return "$('{$selector}').on('slidechange', function() {".$this->toggleHighlightJsName."();} );";
    }
    
    /**
     * Получить js-код, который выполняется при изменении значения слайдера
     * @param string $name - id текстового поля для слайдера
     * 
     * @return string
     */
    protected function getSlideJs($name)
    {
        $js = '';
        // Если задано и максимальное и минимальное значение - выводим диапазон
        $js .= 'if ( ui.values[0] != '.$this->getMinValue().' && ui.values[1] != '.$this->getMaxValue().' )
        {
            $("#'.$name.'").val("'.Yii::t('coreMessages', 'from').' " + ui.values[0] + " '.Yii::t('coreMessages', 'to').' " + ui.values[1]);
            return;
        }';
        // Если только максимальное - выводим "до хх"
        $js .= 'if ( ui.values[0] == '.$this->getMinValue().' && ui.values[1] != '.$this->getMaxValue().' )
        {
            $("#'.$name.'").val("'.Yii::t('coreMessages', 'to').' " + ui.values[1]);
            return;
        }';
        // Если только минимальное - выводим "от хх"
        $js .= 'if ( ui.values[0] != '.$this->getMinValue().' && ui.values[1] == '.$this->getMaxValue().' )
        {
            $("#'.$name.'").val("'.Yii::t('coreMessages', 'from').' " + ui.values[0]);
            return;
        }';
        // Если ничего не задано - выводим заглушку
        $js .= 'if ( ui.values[0] == '.$this->getMinValue().' && ui.values[1] == '.$this->getMaxValue().' )
        {
            $("#'.$name.'").val("'.$this->getNotSetPlaceholder().'");
        }';
        
        return $js;
    }
}