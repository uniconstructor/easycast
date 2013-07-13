<?php

/**
 * Фрагмент формы поиска - "Специализация ведущего"
 * @todo языковые строки
 */
class QSearchFilterMCType extends QSearchFilterBase
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('mctype');
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Специализация ведущего";
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
        
        // Список возможных значений
        $name = $this->getFullInputName('mctype');
        $data = array(
            'emcee'     => 'Ведущий мероприятий',
            'tvshowmen' => 'Телеведущий',
        );
        
        // получаем установленные значения по умолчанию
        $selected = array();
        if ( $params = $this->loadLastSearchParams() )
        {
            $selected = $params['mctype'];
        }
        $htmlOptions['labelOptions'] = array('class' => 'ec-search-filter-inline-input');
        
        // Выводим сами галочки
        $content .= CHtml::checkBoxList($name, $selected, $data, $htmlOptions);
        
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
        $selector  = 'input[name="'.$this->getFullInputName('mctype').'[]"]';
        
        return  "function {$this->collectDataJsName}() {
            var data = {};
            var typeValue  = jQuery('{$selector}:checked');
            
            if ( typeValue.length > 0 )
            {
                var types = [];
                $.each(typeValue, function(index, element){
                    types[index] = element.value;
                });
                data.mctype = types;
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
        $selector  = 'input[name="'.$this->getFullInputName('mctype').'[]"]';
        return  "$('{$selector}').removeAttr('checked');";
    }
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::createAttachInputToggleJs()
     */
    protected function createAttachInputToggleJs()
    {
        $selector  = 'input[name="'.$this->getFullInputName('mctype').'[]"]';
        return "jQuery('{$selector}').change({$this->toggleHighlightJsName});";
    }
}