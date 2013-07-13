<?php

/**
 * Большая форма поиска со всеми возможными критериями
 * Используется при поиске и при создании вакансии для мероприятий
 * (чтобы задать критерии того, кто подходит для вакансии)
 * 
 * Форма собирает себя по кусочкам, для каждого поля используя специальный виджет
 * Виджеты полей общие для фильтров и для формы поиска
 */
class QSearchForm extends QSearchFilters
{
    public $mode = 'form';
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     * 
     * @todo удалить
     */
    public function init()
    {
        //Yii::import('catalog.extensions.search.filters.QSearchFilterBase.QSearchFilterBase');
        //Yii::import('catalog.extensions.search.filters.QSearchFilterBaseSlider.QSearchFilterBaseSlider');
        
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('form');
    }
}