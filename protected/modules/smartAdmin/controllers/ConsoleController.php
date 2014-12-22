<?php

/**
 * Контроллер панели управления новой админкм
 */
class ConsoleController extends SmartAdminController
{
    /**
     * @var string the name of the default action.
     */
    public $defaultAction = 'dashboard';
    
    /**
     * @var array
     */
    public $menuItems = array(
        array(
            'label' => 'Панель управления',
            'url'   => '/smartAdmin/console/dashboard',
            'icon'  => '',
        ),
        array(
            'label' => 'Календарь',
            'url'   => '/smartAdmin/console/calendar',
            'icon'  => '',
        ),
        array(
            'label' => 'Команда',
            'url'   => '/smartAdmin/console/team',
            'icon'  => '',
        ),
        array(
            'label' => 'Проекты',
            'url'   => '/smartAdmin/console/projects',
            'icon'  => '',
        ),
        array(
            'label' => 'Мероприятия',
            'url'   => '/smartAdmin/console/events',
            'icon'  => '',
        ),
        array(
            'label' => 'Анкеты',
            'url'   => '/smartAdmin/console/questionaries',
            'icon'  => '',
        ),
        array(
            'label' => 'Последние действия',
            'url'   => '/smartAdmin/console/feed',
            'icon'  => '',
        ),
        array(
            'label' => 'Настройки',
            'url'   => '/smartAdmin/console/config',
            'icon'  => '',
        ),
        array(
            'label' => 'Разделы каталога',
            'url'   => '/smartAdmin/console/catalog',
            'icon'  => '',
        ),
        array(
            'label' => 'Заказчики',
            'url'   => '/smartAdmin/console/customers',
            'icon'  => '',
        ),
        array(
            'label' => 'Статистика',
            'url'   => '/smartAdmin/console/stat',
            'icon'  => '',
        ),
        array(
            'label' => 'Чат',
            'url'   => '/smartAdmin/console/chat',
            'icon'  => '',
        ),
    );
    
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
            //array('ext.booster.filters.BoosterFilter'),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('dashboard', 'team', 'calendar'),
                'roles'   => array('Admin'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * @see CController::actions()
     */
    public function actions()
    {
        return array(
            'dashboard' => array(
                'class' => 'smartAdmin.controllers.actions.DashboardAction',
            ),
            'team' => array(
                'class' => 'smartAdmin.controllers.actions.TeamAction',
            ),
            'calendar' => array(
                'class' => 'smartAdmin.controllers.actions.CalendarAction',
            ),
        );
    }
}