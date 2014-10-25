<?php

/**
 * Виджет поиска "служебные фильтры"
 * Содержит критерии поиска по полям в базе "статист"(да/нет), "проф. актер"(да/нет) и т. д. 
 */
class QSearchFilterSystem extends QSearchFilterBaseSelect2
{
    /**
     * @var array - список имен input-полей, которые содержатся в фрагменте формы
     */
    protected $elements = array('system', 'operator');
    
    /**
     * @see QSearchFilterBaseSelect2::init()
     */
    public function init()
    {
        parent::init();
        if ( $this->refreshDataOnChange )
        {
            $opSelector = 'input[name="'.$this->getFullInputName('operator').'"]';
            $js = "jQuery('{$opSelector}').change(function(){
                $('body').trigger('{$this->refreshDataEvent}');
            });";
            Yii::app()->clientScript->registerScript('_ecRefreshSystemSearchOperator#'.$this->namePrefix,
                $js, CClientScript::POS_END);
        }
    }
    
    /**
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "Служебные фильтры";
    }
    
    /**
     * @see QSearchFilterBaseSelect2::getContent()
     */
    protected function getContent()
    {
        $content  = '';
        //$selected = 'OR';
        $selected = '';
        
        // добавляем способ объединения условий 
        $name = $this->getFullInputName('operator');
        $data = array(
            'OR'  => 'Хотя бы одно из условий',
            'AND' => 'Обязательны все условия',
        );
        if ( $params = $this->loadLastSearchParams() )
        {
            if ( isset($params['operator']) )
            {
                $selected = $params['operator'];
            }
        }
        $htmlOptions['labelOptions'] = array('class' => 'ec-search-filter-inline-input');
        $content .= CHtml::radioButtonList($name, $selected, $data, $htmlOptions);
        
        $content .= parent::getContent();
        
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
        $opSelector = 'input[name="'.$this->getFullInputName('operator').'"]';
        
        $js = "function {$this->collectDataJsName}() {
            var data  = {};
            var value = $('#{$this->s2Selector}').select2('val');
            if ( Object.keys(value).length > 0 )
            {
                data.{$shortname} = value;
            }
            data.operator = jQuery('{$opSelector}:checked').val();
            return data;
        };";
        
        return $js;
    }
    
    /**
     * Создать функцию, которая проверяет, пуст фильтр или нет 
     * @return string
     * 
     * @todo добавить поддержку всего что ниже IE9 
     * ( http://stackoverflow.com/questions/5533192/how-to-get-object-length-in-jquery )
     */
    protected function createIsEmptyFilterJs()
    {
        return "function {$this->isEmptyJsName}() {
            var data  = {$this->collectDataJsName}();
            var value = $('#{$this->s2Selector}').select2('val');
            if ( ! value )
            {
                return true;
            }
            return false;
        };";
    }
    
    /**
     * @see QSearchFilterBaseSelect2::getMenuVariants()
     */
    protected function getMenuVariants()
    {
        return CatalogFilter::systemFiltersList();
    }
    
    /**
     * Сворачивать ли фрагмент условия при загрузке формы?
     * (Не сворачиваются только те фрагменты, в которых пользователь ранее указал данные,
     * которые запомнились в сессию)
     *
     * @return null
     */
    protected function collapsedAtStart()
    {
        $data = $this->loadLastSearchParams();
        if ( ! isset($data['system']) OR empty($data['system']) )
        {// значений по умолчанию нет - сворачиваем фрпагмент формы
            return true;
        }
        return false;
    }
}