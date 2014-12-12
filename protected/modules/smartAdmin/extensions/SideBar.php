<?php

/**
 * Виджет бокой навигации для темы SmartAdmin
 */
class SideBar extends CWidget
{
    /**
     * @var array
     */
    public $items = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->widget('zii.widgets.CMenu', array(
            'items'            => $this->items,
            'linkLabelWrapper' => 'li',
            'firstItemCssClas' => 'menu-item-parent',
        ));
    }
}