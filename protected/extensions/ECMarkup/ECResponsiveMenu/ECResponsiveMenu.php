<?php

/**
 * Меню участника/заказчика для темы maximal
 */
class ECResponsiveMenu extends CWidget
{
    /**
     * @var array - menu items
     *              each menu item is an array, containing item options
     * Example:
     * array(
     *     // menu item with text only
     *     [0] => array(
     *         'url'  => 'http://example.com/section1',
     *         'text' => 'Menu Item',
     *         'linkOptions' => array('target' => '_blank'),
     *     ),
     *     // menu item with text and icon
     *     [1] => array(
     *         'url'  => 'http://example.com/section2',
     *         'text' => 'Menu Item',
     *         'icon' => '<i aria-hidden="true" class="icon-services"></i>', // you can also use any custom html here
     *         'linkOptions' => array('target' => '_blank'),
     *     ),
     *     // menu item with custom html content
     *     [2] => array(
     *         'url'  => 'http://example.com/section3',
     *         'html' => '<span class="icon"><i aria-hidden="true" class="icon-services"></i></span><span>Menu Item</span>',
     *         'linkOptions' => array('target' => '_blank'),
     *     ),
     * )
     */
    public $items;
    
    /**
     * @var string - режим просмотра: заказчик (customer) или участник (user)
     */
    protected $userMode;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $this->userMode = Yii::app()->getModule('user')->getViewMode();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->userMode === 'customer' )
        {
            $this->render('customer');
        }else
        {
            $this->render('user');
        }
    }
}