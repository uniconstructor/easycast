<?php

/**
 * Фрагмент формы поиска - "Модель"
 * @todo языковые строки
 */
class QSearchFilterModel extends QSearchFilterBase
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('modeltype');
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Специализация модели";
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
        $name = $this->getFullInputName('modeltype');
        
        if ( $this->display == 'form' )
        {// В большой форме поиска показываем все 3 галочки
            $data = array(
                'model'      => 'Модель для съемок',
                'photomodel' => 'Фотомодель',
                'promomodel' => 'Промо-модель',
            );
        }elseif ( $this->display == 'filter' )
        {// В разделе моделей убираем "промо-модель" 
            $data = array(
                'model'      => 'Модель для съемок',
                'photomodel' => 'Фотомодель',
            );
        }
        
        // получаем установленные значения по умолчанию
        $selected = array();
        if ( $params = $this->loadLastSearchParams() )
        {
            $selected = $params['modeltype'];
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
        $selector  = 'input[name="'.$this->getFullInputName('modeltype').'[]"]';
        
        return  "function {$this->collectDataJsName}() {
            var data = {};
            var typeValue  = jQuery('{$selector}:checked');
            
            if ( typeValue.length > 0 )
            {
                var types = [];
                $.each(typeValue, function(index, element){
                    types[index] = element.value;
                });
                data.modeltype = types;
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
        $selector  = 'input[name="'.$this->getFullInputName('modeltype').'[]"]';
        return  "$('{$selector}').removeAttr('checked');";
    }
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::createAttachInputToggleJs()
     */
    protected function createAttachInputToggleJs()
    {
        $selector  = 'input[name="'.$this->getFullInputName('modeltype').'[]"]';
        return "jQuery('{$selector}').change({$this->toggleHighlightJsName});";
    }
}