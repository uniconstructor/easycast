<?php

/**
 * Класс для поиска по параметрам тела
 */
class QSearchFilterBody extends QSearchFilterBaseSlider
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('chestsize', 'waistsize', 'hipsize');
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Параметры тела";
    }
    
    /**
     * Получить минимальное значение слайдера
     * @return int
     */
    protected function getMinValue()
    {
        return 30;
    }
    
    /**
     * Получить максимальное значение слайдера
     * @return int
     */
    protected function getMaxValue()
    {
        return 110;
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
        
        foreach ( $this->elements as $element )
        {// перебираем все параметры тела (обхват груди, талия, бедра) и для каждого рисуем слайдер
            $content .= $this->getSliderWidget($element);
        }
    
        return $content;
    }
    
    /**
     * Получить html-код одного слайдера
     * 
     * @param string $element - тип слайдера ('chestsize', 'waistsize', 'hipsize')
     * @return string
     * 
     * @todo выровнять текстовые блоки
     */
    protected function getSliderWidget($element)
    {
        $content     = '';
        $shortname   = $this->filter->shortname;
        $textFieldId = 'search_'.$shortname.'_'.$element.'_slider_info';
        
        // задаем значения по умолчанию 
        $values = array(
            0 => $this->getMinValue(),
            1 => $this->getMaxValue(),
        );
        if ( $defaults = $this->loadLastSearchParams() )
        {
            if ( isset($defaults['min'.$element]) )
            {
                $values[0] = $defaults['min'.$element];
            }
            if ( isset($defaults['max'.$element]) )
            {
                $values[1] = $defaults['max'.$element];
            }
        }
        
        // выводим пояснение к параметру фильтра
        $content .= "<label for='{$textFieldId}' style='display:inline;'>{$this->getSliderCaption($element)}:&nbsp;</label>";
        
        // Выводим окошко с выбранными результатами
        $content .= CHtml::textField(null, $this->getDefaultSliderInfo($values[0], $values[1]), array(
            'id'       => $textFieldId,
            'class'    => 'text-right',
            'class'    => 'ec-search-filter-slider-info',
            'disabled' => 'disabled',
        ));
        
        // выводим сам виджет со слайдером
        $content .= $this->widget('zii.widgets.jui.CJuiSlider',array(
            // additional javascript options for the slider plugin
            'options' => array(
                'min' => $this->getMinValue(),
                'max' => $this->getMaxValue(),
                'range'  => true,
                'values' => $values,
                'slide'  => 'js:function( event, ui ) {'.$this->getSlideJs($textFieldId).'}',
            ),
            'htmlOptions'=>array(
                'name' => $this->getFullInputName($element),
            ),
        ), true);
        
        return $content;
    }
    
    /**
     * Получить пояснение к слайдеру
     * 
     * @param string $element - тип слайдера ('chestsize', 'waistsize', 'hipsize')
     * @return string
     * 
     * @todo языковые строки
     */
    protected function getSliderCaption($element)
    {
        switch ( $element )
        {
            case 'chestsize': return 'Обхват груди';
            case 'waistsize': return 'Талия';
            case 'hipsize':   return 'Бедра';
        }
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
        $selectors = $this->getJSONSelectors();
        
        // В цикле перебираем все селекторы слайдеров и собираем с каждого
        // максимальное и минимальное значение
        return "function {$this->collectDataJsName}() {
            var data = {};
            var selectors = {$selectors};
            
            $.each(selectors, function(element, selector){ 
                var sliderObj = $(selector);
                
                if ( sliderObj.slider( 'values', 0 ) != ".$this->getMinValue()." )
                {
                    data['min' + element] = $(selector).slider('values',0);
                }
                if ( sliderObj.slider( 'values', 1 ) != ".$this->getMaxValue()." )
                {
                    data['max' + element] = $(selector).slider('values',1);
                }
            });
            
            return data;
        };";
    }
    
    /**
     * Получить массив селекторов для обращения к слайдерам виджета в формате JSON
     * 
     * @return string
     */
    protected function getJSONSelectors()
    {
        $selectors = array();
        foreach ( $this->elements as $element )
        {// создаем селектор для каждого слайдера
            $selectors[$element] = 'div[name="'.$this->getFullInputName($element).'"]';
        }
        // собираем все слайдеры виджета в массив, чтобы для каждого из них создать код получения данных
        return CJSON::encode($selectors);
    }
    
    /**
     * Получить js-код для очистки выбранных пользователем значений в фрагменте формы
     * (Этот JS очищает только данные на стороне клиента. Код уникальный для каждого элемента)
     *
     * @return string
     */
    protected function createClearFormDataJs()
    {
        $selectors = $this->getJSONSelectors();
        
        return "var selectors = {$selectors};
        $.each(selectors, function(element, selector) {
            var textSelector = '#search_{$this->filter->shortname}_' + element + '_slider_info';
            $(selector).slider('option', 'values', [{$this->getMinValue()}, {$this->getMaxValue()}]);
            $(textSelector).val({$this->getMinValue()} + '-' + {$this->getMaxValue()});
        });";
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
        $selectors = $this->getJSONSelectors();
        
        return "var selectors = {$selectors};
        $.each(selectors, function(element, selector) {
            $(selector).on('slidechange', function() {".$this->toggleHighlightJsName."();} );
        });";
    }
}