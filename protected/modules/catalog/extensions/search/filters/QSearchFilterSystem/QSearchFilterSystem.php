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
     * @see QSearchFilterBase::getTitle()
     */
    protected function getTitle()
    {
        return "СЛУЖЕБНЫЕ ФИЛЬТРЫ";
    }
    
    /**
     * @see QSearchFilterBaseSelect2::getContent()
     */
    protected function getContent()
    {
        $content  = '';
        $selected = 'OR';
        
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
     * (non-PHPdoc)
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
            var data = {};
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
     * @see QSearchFilterBaseSelect2::getMenuVariants()
     */
    protected function getMenuVariants()
    {
        return array(
            'isactor' => 'Профессиональный актер',
            'hasfilms' => 'Есть опыт съемок',
            'isemcee' => 'Ведущий мероприятий',
            'isparodist' => 'Умеет пародировать',
            'istwin' => 'Двойник',
            'ismodel' => 'Модель',
            'isphotomodel' => 'Фотомодель',
            'ispromomodel' => 'Промо-модель',
            'isdancer' => 'Умеет танцевать',
            'hasawards' => 'Есть звания или награды',
            'isstripper' => 'Танцует стриптиз',
            'issinger' => 'Занимается вокалом',
            'ismusician' => 'Музыкант',
            'issportsman' => 'Спортсмен',
            'isextremal' => 'Экстремал',
            'isathlete' => 'Атлетическое телосложение',
            'hasskills' => 'Указаны дополнительные навыки',
            'hastricks' => 'Каскадер',
            'haslanuages' => 'Владеет иностранным языком',
            'hasinshurancecard' => 'Есть медицинская страховка',
            'hastatoo' => 'Есть татуировки',
            'isamateuractor' => 'Непрофессиональный актер',
            'istvshowmen' => 'Телеведущий',
            'isstatist' => 'Статист/типаж',
            'ismassactor' => 'Артист массовх сцен',
            'istheatreactor' => 'Актер театра',
            'ismediaactor' => 'Медийный актер',
        );
    }
}