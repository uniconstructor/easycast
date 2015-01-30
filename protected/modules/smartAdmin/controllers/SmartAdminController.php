<?php

/**
 * Базовый класс для всех контроллеров новой админки
 * 
 * @todo убрать enableJavaScript=false после подключения новой версии bootstrap
 */
abstract class SmartAdminController extends Controller
{
    /**
     * @var string 
     */
    public $layout = '//layouts/main';
    /**
     * @var array - левая панель навигации в меню, массив из элементов 'label', 'url' и 'htmlOptions'
     *              Для вложенных элементов меню используется параметр items
     *              Чтобы изначально развернуть пункт меню используйте переметр open
     *              Пример:
     *              array(
     *                  array(
     *                      'label' => 'Item1',
     *                      'url'   => '...',
     *                      'htmlOptions' => array(),
     *                      'items' => array(
     *                          array(
     *                              'label' => 'Item1/subItem1',
     *                              'url'   => '...',
     *                              'open'  => true,
     *                          ),
     *                          array(
     *                              'label' => 'Item1/subItem2',
     *                              'url'   => '...',
     *                          ),
     *                          ...
     *                      ),
     *                  ),
     *                  ...
     *              );
     */
    public $menuItems = array();
    /**
     * @var string - Основной заголовок страницы
    */
    public $pageHeader;
    /**
     * @var string
     */
    public $subTitle;
    /**
     * @var array - массив с дополнительными графиками вверху страницы
     */
    public $sparks = array();
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        if ( Yii::app()->request->isAjaxRequest )
        {
            $this->layout = '//layouts/ajax/_page';
        }
        parent::init();
    }
    
    /**
     * @return array
     *
     * @todo настроить проверку прав на основе RBAC
     */
    public function filters()
    {
        $baseFilters = parent::filters();
        $newFilters  = array(
            'accessControl',
            // фильтр для подключения YiiBooster 4.x (bootstrap 3.x)
            array(
                'ext.booster.filters.BoosterFilter',
            ),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
    }
}