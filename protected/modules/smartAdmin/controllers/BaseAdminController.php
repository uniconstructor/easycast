<?php

/**
 * Базовый класс для всех контроллеров новой админки
 * 
 * @todo убрать enableJavaScript=false после подключения новой версии bootstrap
 */
abstract class BaseAdminController extends Controller
{
    /**
     * @var string - 
     */
    public $layout = '//layouts/index';
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
     *                      'open'  => true,
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
     *              
     */
    public $sideBar = array();
    /**
     * @var string
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
        // для просмотра заявок в расширенном виде: убираем все скрипты и стили
        // (они все равно не совместимы с админской темой)
        // и используем только то что подключается из нее
        Yii::app()->clientScript->enableJavaScript = false;
        // в админке переключаемся на специальную тему оформления
        Yii::app()->setTheme('admin');
         
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