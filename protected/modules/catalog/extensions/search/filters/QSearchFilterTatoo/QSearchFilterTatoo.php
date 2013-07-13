<?php

/**
 * Фрагмент формы поиска - "Татуировки"
 * @todo языковые строки
 */
class QSearchFilterTatoo extends QSearchFilterBase
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('tatoo');
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Татуировки";
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
        
        $name = $this->getFullInputName('tatoo');
        $data = array(
            '1' => 'Только с татуировками',
            '0' => 'Только без татуировок',
        );
        
        $selected = null;
        if ( $params = $this->loadLastSearchParams() )
        {
            $selected = $params['tatoo'];
        }
        
        $htmlOptions['labelOptions'] = array('class' => 'ec-search-filter-inline-input');
        $content .= CHtml::radioButtonList($name, $selected, $data, $htmlOptions);
        
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
        $js = "function {$this->collectDataJsName}() {";
        $js .= 'var data = {};';
        foreach ( $this->inputSelectors as $element => $selector )
        {// выбираем все элементы по имени, и собираем значения
            $js .= "data.{$element} = jQuery('{$selector}:checked').val();";
        }
        $js .= "return data;";
        $js .= "};";
        
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
        foreach ( $this->inputSelectors as $element=>$selector )
        {// выбираем все элементы по имени, и сбрасываем значения
            $js .= "$('{$selector}').removeAttr('checked')";
        }
        return $js;
    }
}