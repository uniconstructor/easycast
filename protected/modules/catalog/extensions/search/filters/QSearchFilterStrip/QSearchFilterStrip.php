<?php

/**
 * Фрагмент формы поиска - "стриптиз"
 * @todo языковые строки
 * @todo перенести верстку во внешние файлы
 */
class QSearchFilterStrip extends QSearchFilterBase
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('striptype', 'striplevel');
    
    /**
     * Получить заголовок фрагмента формы поиска (например "пол", "возраст" и т. п.)
     * @return string
     */
    protected function getTitle()
    {
        return "Стриптиз";
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
        
        $content .= '<table>';
        // тип
        $content .= '<tr>';
        $content .= $this->createStripElement('striptype');
        $content .= '</tr>';
        // уровень
        $content .= '<tr>';
        $content .= $this->createStripElement('striplevel');
        $content .= '</tr>';
        
        $content .= '</table>';
        
        return $content;
    }
    
    /**
     * Получить html-код одного параметра виджета (тип/уровень)
     * @param string $type - какой элемент отображать ('striptype', 'striplevel')
     *
     * @return string
     */
    protected function createStripElement($type)
    {
        $content = '';
        
        // пояснение
        $content .= '<td>'.$this->getElementLabel($type).'</td>';
        
        // Уровень (любитель/профессионал)
        $name = $this->getFullInputName($type);
        $data = QActivityType::model()->activityVariants($type);
        
        // получаем значения по умолчанию
        $selected = $this->getSelectedDefaults($type);
        
        $htmlOptions['labelOptions'] = array('style' => 'display:inline;');
        $content .= '<td>'.CHtml::checkBoxList($name, $selected, $data, $htmlOptions).'</td>';
        
        return $content;
    }
    
    /**
     * Получить пояснение для элемента фильтра
     * @param string $type - тип отображаемого элемента ('striptype', 'striplevel')
     * @return string
     */
    protected function getElementLabel($type)
    {
        switch ( $type )
        {
            case 'striptype':  return '<b>'.Yii::t('coreMessages', 'type').'</b>';
            case 'striplevel': return '<b>'.Yii::t('coreMessages', 'level').'</b>';
        }
    }
    
    /**
     * Получить значение по умолчанию для одного параметра виджета
     * @param string $element - название параметра внутри виджета ('isstripper', 'striptype', 'striplevel')
     * @return null
     */
    protected function getSelectedDefaults($element)
    {
        $selected = '';
        if ( $params = $this->loadLastSearchParams() )
        {// получаем значения по умолчанию (если есть)
            if ( isset($params[$element]) )
            {
                $selected = $params[$element];
            }
        }
        
        return $selected;
    }
    
    /**
     * Получить JS-код, который реагирует на событие собирает все данные из фрагмента формы в JSON-массив
     * (для отправки по AJAX, чтобы тут же обновлять данные поиска в сессии или динамически обновлять содержимое поиска)
     * (этот метод - индивидуальный для каждого фильтра)
     * 
     * @return string
     * 
     * @todo добавить поддержку всего что ниже IE9
     * ( http://stackoverflow.com/questions/5533192/how-to-get-object-length-in-jquery )
     */
    protected function createCollectFilterDataJs()
    {
        $typeSelector  = 'input[name="'.$this->getFullInputName('striptype').'[]"]';
        $levelSelector = 'input[name="'.$this->getFullInputName('striplevel').'[]"]';
        
        return  "function {$this->collectDataJsName}() {
            var data = {};
            var typeValue  = jQuery('{$typeSelector}:checked');
            var levelValue = jQuery('{$levelSelector}:checked');
            
            if ( typeValue.length > 0 )
            {
                var types = [];
                $.each(typeValue, function(index, element){
                    types[index] = element.value;
                });
                data.striptype = types;
            }
            if ( levelValue.length > 0 )
            {
                var levels = [];
                $.each(levelValue, function(index, element){
                    levels[index] = element.value;
                });
                data.striplevel = levels;
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
        $typeSelector  = 'input[name="'.$this->getFullInputName('striptype').'[]"]';
        $levelSelector = 'input[name="'.$this->getFullInputName('striplevel').'[]"]';
        $js = '';
        $js .= "$('{$typeSelector}').removeAttr('checked');";
        $js .= "$('{$levelSelector}').removeAttr('checked');";
        return $js;
    }
    
    /**
     * (non-PHPdoc)
     * @see QSearchFilterBase::createAttachInputToggleJs()
     */
    protected function createAttachInputToggleJs()
    {
        $typeSelector  = 'input[name="'.$this->getFullInputName('striptype').'[]"]';
        $levelSelector = 'input[name="'.$this->getFullInputName('striplevel').'[]"]';
        $js = '';
        $js .= "jQuery('{$typeSelector}').change({$this->toggleHighlightJsName});";
        $js .= "jQuery('{$levelSelector}').change({$this->toggleHighlightJsName});";
    
        return $js;
    }
}