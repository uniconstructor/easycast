<?php

/**
 * Базовый класс для всех виджетов-филтров, использующих плагин "select2"
 * @todo языковые строки
 */
class QSearchFilterBaseSelect2 extends QSearchFilterBase
{
    /**
     * @var string - текст-заглушка, который отображается в поле, когда ничего не выбрано
     */
    public $defaultSelect2Placeholder = 'Нажмите чтобы выбрать';
    
    /**
     * @var string - селектор для элемента select2
     */
    protected $s2Selector;
    
    /**
     * @see QSearchFilterBase::init()
     */
    public function init()
    {
        // устанавливаем id элемента в котором расположен select2
        // @todo придумать более элегантное решение, использовать $this->filter->shortname
        $shortname = $this->getShortName();
        $this->s2Selector = self::defaultPrefix().$shortname.'_'.$shortname;
    
        parent::init();
    }
    
    /**
     * @see QSearchFilterBase::getContent()
     */
    protected function getContent()
    {
        $content   = '';
        // короткое название критерия поиска
        $shortname = $this->getShortName();
        // получаем полное имя html-элемента
        $name      = $this->getFullInputName($shortname);
        // получаем список значений для выпадающего меню и одновременно выставляем значения по умолчанию
        $variants  = $this->createSelectVariants();
        
        // Создаем обычный выпадающий список, чтобы потом создать из него select2
        $content .= CHtml::dropDownList($name, null, $this->getMenuVariants(), array(
            'id'       => $this->s2Selector,
            'multiple' => 'multiple',
            'style'    => 'width:100%;',
            'options'  => $variants,
        ));
    
        // Создаем select2 с нужными настройками
        $content .= $this->widget('ext.select2.ESelect2',array(
            'name'     => $this->getFullInputName($shortname),
            'selector' => '#'.$this->s2Selector,
            'data'     => $variants,
            'options'  => $this->createSelect2Options(),
        ), true);
    
        return $content;
    }
    
    /**
     * @see QSearchFilterBase::createCollectFilterDataJs()
     * 
     * @todo добавить поддержку всего что ниже IE9
     * ( http://stackoverflow.com/questions/5533192/how-to-get-object-length-in-jquery )
     */
    protected function createCollectFilterDataJs()
    {
        $shortname = $this->getShortName();
        return "function {$this->collectDataJsName}() {
            var data  = {};
            var value = $('#{$this->s2Selector}').select2('val');
            if ( Object.keys(value).length > 0 )
            {
                data.{$shortname} = value;
            }
            return data;
        };";
    }
    
    /**
     * @see QSearchFilterBase::createClearFormDataJs()
     */
    protected function createClearFormDataJs()
    {
        return "$('#{$this->s2Selector}').select2('val', '');";
    }
    
    /**
     * @see QSearchFilterBase::createAttachInputToggleJs()
     */
    protected function createAttachInputToggleJs()
    {
        return "$('#{$this->s2Selector}').on('change', function() {".$this->toggleHighlightJsName."();} );";
    }
    
    /**
     * Получить настройки для плагина select2
     *
     * @return array
     */
    protected function createSelect2Options()
    {
        return array(
            // текст-заглушка
            'placeholder'    => $this->getTitle(),
            // разрешить удалять варианты из списка
            'allowClear'     => true,
            // select не закрывается, чтобы можно было быстро выбрать несколько вариантов
            'closeOnSelect'  => false,
            // отсылать событие change каждый раз при изменении данных
            'triggerChange ' => true,
            // максимальная ширина всегда
            'width'          => '100%',
        );
    }
    
    /**
     * Получить список вариантов для выпадающего меню (DropDownList) и установить значения по умолчанию
     *
     * @return array
     */
    protected function createSelectVariants()
    {
        $options  = array();
        $selected = array();
    
        // Получаем варианты для выпадающего меню
        $variants = $this->getMenuVariants();
    
        if ( $params = $this->loadLastSearchParams() AND isset($params[$this->getShortName()]) )
        {// получаем значения по умолчанию (если они были)
            $selected = $params[$this->getShortName()];
        }
    
        // создаем массив пунктов для выпадающего меню, и устанавливаем значения по умолчанию
        foreach ( $variants as $value=>$label )
        {
            $option = array();
            $option['label'] = $label;
            if ( in_array($value, $selected) )
            {
                $option['selected'] = 'selected';
            }
            $options[$value] = $option;
        }
    
        return $options;
    }
    
    /**
     * Получить список вариантов для выпадающего меню
     *
     * @return array
     */
     protected function getMenuVariants()
     {
         return QActivityType::model()->activityVariants($this->getShortName());
     }

     /**
      * Получить короткое название критерия поиска
      * (как правило оно совпадает с названием поля или связи (relation) в модели Questionary)
      *
      * @return string
      */
     protected function getShortName()
     {
         return $this->elements[0];
     }
}