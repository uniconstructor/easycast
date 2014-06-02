<?php

/**
 * Фрагмент формы поиска - "Имя или фамилия"
 * @todo языковые строки
 * @todo создать базовый класс для поиска по текстовым полям
 */
class QSearchFilterName extends QSearchFilterBase
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('name');
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Имя";
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
        
        $name = $this->getFullInputName('name');
        
        // Получаем данные по умолчанию
        $default = '';
        if ( $params = $this->loadLastSearchParams() )
        {
            $default = $params['name'];
        }
        $htmlOptions = array('placeholder' => 'Имя или фамилия актера');
        
        // Выводим текстовое поле для ФИО
        $content .= CHtml::textField($name, $default, $htmlOptions);
        
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
        $selector = $this->inputSelectors['name'];
        
        return  "function {$this->collectDataJsName}() {
            var data  = {};
            var value = jQuery.trim(jQuery('{$selector}').val());
            
            if ( value.length > 0 )
            {
                data.name = value;
            }
            
            return data;
        };";
    }
    
    /**
     * Получить js-код для очистки выбранных пользователем значений в фрагменте формы
     * (Этот JS очищает только данные на стороне клиента. Код уникальный для каждого элемента)
     *
     * @return string
     */
    protected function createClearFormDataJs()
    {
        $selector = $this->inputSelectors['name'];
        return "$('{$selector}').val('')";
    }
}