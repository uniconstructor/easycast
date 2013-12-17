<?php

/**
 * Класс сборки фрагмента поискового запроса для фильтра "Служебные фильтры"
 */
class QSearchHandlerSystem extends QSearchHandlerBase 
{
    /**
     * Получить массив параметров для подстановки в CDbCriteria при поиске
     *
     * @return CDbCriteria|null - условие поиска по фильтру или null если фильтр не используется
     */
    protected function createCriteria()
    {
        $data          = $this->getFilterData();
        $fields        = $data['system'];
        $allowedFields = $this->getAllowedFieldVariants();
        $operators     = array('AND', 'OR');
        $operator      = 'OR';
        
        if ( isset($data['operator']) )
        {
            $operator = $data['operator'];
        }
        var_dump($operator);
        if ( ! in_array($operator, $operators) )
        {// защита от SQL-injection
            // @todo записать ошибку в лог
            $operator = 'OR'; 
        }
    
        $criteria = new CDbCriteria();
        foreach ( $fields as $field )
        {// для каждого раздела каталога получаем свой критерий поиска
            if ( ! in_array($field, $allowedFields) )
            {// @todo записать ошибку в лог
                continue;
            }
            $criteria->compare($field, 1, false, $operator);
        }
    
        return $criteria;
    }
    
    /**
     * Получить список разрешенных имен полей (дополнительная проверка безопасности)
     */
    protected function getAllowedFieldVariants()
    {
        $variants = array(
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
        
        return array_keys($variants);
    }
}