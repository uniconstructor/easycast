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
            'iconOptions' => array('class' => 'fa-home'),
        ),
        array(
            'label' => 'Календарь',
            'url'   => '/smartAdmin/console/calendar',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-calendar'),
        ),
        array(
            'label' => 'Команда',
            'url'   => '/smartAdmin/console/team',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-group'),
        ),
        array(
            'label' => 'Проекты',
            'url'   => '/smartAdmin/console/projects',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-puzzle-piece'),
        ),
        array(
            'label' => 'Мероприятия',
            'url'   => '/smartAdmin/console/events',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-list-alt'),
        ),
        array(
            'label' => 'Анкеты',
            'url'   => '/smartAdmin/console/questionaries',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-book'),
        ),
        array(
            'label' => 'Последние действия',
            'url'   => '/smartAdmin/console/feed',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-rss'),
        ),
        array(
            'label' => 'Настройки',
            'url'   => '/smartAdmin/console/config',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-cogs'),
        ),
        array(
            'label' => 'Разделы каталога',
            'url'   => '/smartAdmin/console/catalog',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-folder-open'),
        ),
        array(
            'label' => 'Заказчики',
            'url'   => '/smartAdmin/console/customers',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-book'),
        ),
        array(
            'label' => 'Организации',
            'url'   => '/smartAdmin/console/organizations',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-building'),
        ),
        array(
            'label' => 'Статистика',
            'url'   => '/smartAdmin/console/stat',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-bar-chart'),
        ),
        array(
            'label' => 'Чат',
            'url'   => '/smartAdmin/console/chat',
            'icon'  => '',
            'iconOptions' => array('class' => 'fa-comments'),
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
                'actions' => array('xupload', 'dashboard', 'team', 'calendar', 'projects'),
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
            //'xupload' => array(
            //    'class' => 'xupload.actions.S3XUploadAction',
            //),
            'cockpit' => array(
                'class' => 'application.actions.CockpitAction',
            ),
            'dashboard' => array(
                'class' => 'smartAdmin.actions.DashboardAction',
            ),
            'team' => array(
                'class' => 'smartAdmin.actions.TeamAction',
            ),
            'calendar' => array(
                'class' => 'smartAdmin.actions.CalendarAction',
            ),
            'projects' => array(
                'class' => 'smartAdmin.actions.ProjectsAction',
            ),
        );
    }
}