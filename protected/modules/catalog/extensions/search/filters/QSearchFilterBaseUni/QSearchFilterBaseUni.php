<?php

/**
 * Базовый класс для виджетов поиска по музыкальному и театральному ВУЗу
 */
class QSearchFilterBaseUni extends QSearchFilterBaseSelect2
{
    /**
     * Получить список вариантов для выпадающего меню
     *
     * @return array
     */
    protected function getMenuVariants()
    {
        return QUniversityInstance::model()->getUniversityList($this->universityType);
    }
    
    /**
     * Получить короткое название критерия поиска по вузу
     *
     * @return string
     */
    protected function getShortName()
    {
        throw new CHttpException('500', 'getShortName() должен быть наследован');
    }
    
    /**
     * Получить короткое название критерия поиска по году окончания ВУЗа
     *
     * @return string
     */
    protected function getSliderShortName()
    {
        throw new CHttpException('500', 'getSliderShortName() должен быть наследован');
    }
    
    /**
     * Получить минимальное значение слайдера "год выпуска"
     * @return int
     */
    protected function getMinValue()
    {
        return $this->getMaxValue() - 15;
    }
    
    /**
     * Получить максимальное значение слайдера "год выпуска"
     * @return int
     */
    protected function getMaxValue()
    {
        return date('Y') + 5;
    }
    
    /**
     * Получить текст, который отображается, когда в слайдере ничего не выбрано
     * @return string
     */
    protected function getNotSetPlaceholder()
    {
        return "Любой";
    }
    
    /**
     * Получить значение в текстовом поле над слайдером сразу же при загрузке страницы
     * @param int $min
     * @param int $max
     * @return string
     * 
     * @todo вынести в behavior чтобы не дублировать
     */
    protected function getDefaultSliderInfo($min, $max)
    {
        $minLimit = $this->getMinValue();
        $maxLimit = $this->getMaxValue();
    
        if ( $min == $minLimit AND $max == $maxLimit )
        {// не задан ни один параметр - выводим заглушку
            return $this->getNotSetPlaceholder();
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
     * (non-PHPdoc)
     * @see QSearchFilterBaseSelect2::getContent()
     */
    protected function getContent()
    {
        $content = '';
        
        // получаем из родительского класса готовый код select2 со списком ВУЗов
        $content .= parent::getContent();
        
        // после него добавляем слайдер с годом выпуска
        $shortname = $this->getSliderShortName();
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
        $content .= '<br>Год окончания: ';
        $content .= CHtml::textField(null, $this->getDefaultSliderInfo($values[0], $values[1]), array(
            'id'       => 'search_'.$shortname.'_slider_info',
            'style'    => 'ec-search-filter-slider-info',
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
                'slide'  => 'js:function(event, ui) {'.$this->getSlideJs('search_'.$shortname.'_slider_info').'}',
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
        // определяем короткое имя и селектор для select2 и для слайдера
        $select2Name = $this->getShortName();
        $sliderName  = $this->getSliderShortName();
        $sliderSelector = 'div[name="'.$this->getFullInputName($sliderName).'"]';
        
        return "function {$this->collectDataJsName}() {
            var data = {};
            
            var universities = $('#{$this->s2Selector}').select2('val');
            if ( Object.keys(universities).length > 0 )
            {
                data.{$select2Name} = universities;
            }
            
            var sliderObj = $('{$sliderSelector}');
            if ( sliderObj.slider( 'values', 0 ) != ".$this->getMinValue()." )
            {
                data.min{$sliderName} = $('{$sliderSelector}').slider( 'values', 0 );
            }
            if ( sliderObj.slider( 'values', 1 ) != ".$this->getMaxValue()." )
            {
                data.max{$sliderName} = $('{$sliderSelector}').slider( 'values', 1 )
            }
        
            return data;
        };";
    }
    
    /**
     * @see QSearchFilterBase::createClearFormDataJs()
     */
    protected function createClearFormDataJs()
    {
        $sliderSelector = 'div[name="'.$this->getFullInputName($this->getSliderShortName()).'"]';
        // Получаем код очистки select2 из родительского класса
        $js = parent::createClearFormDataJs();
        $js .= "$('{$sliderSelector}').slider('option', 'values', [ ".$this->getMinValue().", ".$this->getMaxValue()." ] );";
        $js .= '$("#search_'.$this->getSliderShortName().'_slider_info").val("'.$this->getNotSetPlaceholder().'");';
        
        return $js;
    }
    
    /**
     * @see QSearchFilterBase::createAttachInputToggleJs()
     */
    protected function createAttachInputToggleJs()
    {
        $sliderSelector = 'div[name="'.$this->getFullInputName($this->getSliderShortName()).'"]';
        $js = parent::createAttachInputToggleJs();
        $js .= "$('{$sliderSelector}').on('slidechange', function() {".$this->toggleHighlightJsName."();} );";
        
        return $js;
    }
    
    /**
     * Получить js-код, который выполняется при изменении значения слайдера
     * @param string $name - id текстового поля для слайдера
     *
     * @return string
     * 
     * @todo вынести во внешний JS чтобы не дублировать
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