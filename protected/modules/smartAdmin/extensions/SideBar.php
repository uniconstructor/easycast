<?php

/**
 * Виджет боковой навигации для темы SmartAdmin
 * 
 * @todo языковые строки
 */
class SideBar extends CWidget
{
    /**
     * @var array - элементы меню: в добавлении к стандартным параметрам могут содержать опции:
     *              'icon'            => 'fa-inbox', // иконка font awesome
     *              'iconOptions'     => array(),    // htmlOptions для тега иконки 
     *              'iconHint'        => 'new',      // 
     *              'iconHintOptions' => array(),    // 
     *              'count'           => 35,         // счетчик
     *              'countOptions'    => array(),    // htmlOptions для тега счетчика
     *              'append'          => '<b>custom html</b>', // @todo
     *              'prepend'         => '<b>custom html</b>', // @todo
     */
    public $items = array();
    /**
     * @var string the template used to render an individual menu item. In this template,
     *      the token "{menu}" will be replaced with the corresponding menu link or text.
     *      If this property is not set, each menu will be rendered without any decoration.
     *      This property will be overridden by the 'template' option set in individual menu items via {@items}.
     * @since 1.1.1
     */
    public $itemTemplate;
    /**
     * @var boolean whether the labels for menu items should be HTML-encoded. Defaults to true.
     */
    public $encodeLabel     = true;
    /**
     * @var string the CSS class to be appended to the active menu item. Defaults to 'active'.
     *      If empty, the CSS class of menu items will not be changed.
     */
    public $activeCssClass  = 'active';
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     *      matches the currently requested route. Defaults to true.
     * @since 1.1.3
     */
    public $activateItems   = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     *      The activated parent menu items will also have its CSS classes appended with {@link activeCssClass}.
     *      Defaults to false.
     */
    public $activateParents = true;
    /**
     * @var boolean whether to hide empty menu items. An empty menu item is one whose 'url' option is not
     * set and which doesn't contain visible child menu items. Defaults to true.
     */
    public $hideEmptyItems  = false;
    /**
     * @var array
     */
    public $iconOptions = array(
        'class' => 'fa fa-lg fa-fw ',
    );
    /**
     * @var array
     */
    public $countOptions = array(
        'class' => 'badge pull-right badge-default ',
    );
    /**
     * @var array
     */
    public $iconHintOptions = array();
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        //CVarDumper::dump(count($this->items), 10, true);echo '|';
        //CVarDumper::dump(count($items), 10, true);
        $this->widget('zii.widgets.CMenu', array(
            'items'             => $this->normalizeItems($this->items),
            //'linkLabelWrapper'  => 'li',
            //'firstItemCssClass' => 'menu-item-parent',
            'hideEmptyItems'    => false,
            'encodeLabel'       => false,
        ));
    }
    
    /**
     * Setter
     * 
     * @param  array $items
     * @return void
     */
    public function normalizeItems($items)
    {
        $newItems = array();
        foreach ( $items as $item )
        {
            //$newItem = CMap::copyFrom($item);
            $template = array(
                'url'            => '',
                'visible'        => '1',
                'active'         => '',
                'label'          => '',
                //'template'       => '',
                'linkOptions'    => array(),
                'itemOptions'    => array(),
                'submenuOptions' => array(),
            );
            $item    = CMap::mergeArray($template, $item);
            $newItem = array(
                'url'            => $item['url'],
                'visible'        => $item['visible'],
                'active'         => $item['active'],
                'label'          => $item['label'],
                //'template'       => $item['template'],
                'linkOptions'    => $item['linkOptions'],
                'itemOptions'    => $item['itemOptions'],
                'submenuOptions' => $item['submenuOptions'],
            );
            if ( isset($item['count']) )
            {// счетчик
                if ( isset($item['countOptions']) AND is_array($item['countOptions']) )
                {
                    if ( isset($item['countOptions']['class']) AND isset($this->countOptions['class']) )
                    {
                        $this->countOptions['class'] .= ' '.$item['countOptions']['class'];
                        unset($item['countOptions']['class']);
                    }
                    $this->countOptions = CMap::mergeArray($this->countOptions, $item['countOptions']);
                }
                $newItem['label'] .= ' '.CHtml::tag('span', $this->countOptions, $item['count']);
            }
            if ( isset($item['icon']) )
            {// иконка
                if ( isset($item['iconOptions']) AND is_array($item['iconOptions']) )
                {// свойства иконки
                    if ( isset($item['iconOptions']['class']) AND isset($this->iconOptions['class']) )
                    {
                        $item['iconOptions']['class'] = $this->iconOptions['class'].' '.$item['iconOptions']['class'];
                    }
                    $item['iconOptions'] = CMap::mergeArray($this->iconOptions, $item['iconOptions']);
                }else
                {
                    $item['iconOptions'] = $this->iconOptions;
                }
                $iconHint = '';
                if ( isset($item['iconHint']) )
                {// свойства надписи на иконке
                    $this->iconHintOptions = CMap::mergeArray($this->iconHintOptions, $item['iconHintOptions']);
                    $iconHint = CHtml::tag('em', $this->iconHintOptions, $item['iconHint']);
                }
                $newItem['label'] = CHtml::tag('i', $item['iconOptions'], $iconHint).' '.
                    CHtml::tag('span', array('class' => 'menu-item-parent'), $newItem['label']);
            }
            if ( isset($item['items']) )
            {// вложенные элементы
                if ( ! isset($item['url']) )
                {
                    $newItem['url'] = '#';
                }
                $newItem['items'] = $this->normalizeItems($item['items']);
                /*$newItem['itemOptions'] = array(
                    'class' => 'menu-item-parent',
                );*/
            }
            $newItems[] = $newItem;
        }
        return $newItems;
    }
}