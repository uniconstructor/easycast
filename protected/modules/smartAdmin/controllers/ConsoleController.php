<?php

class ConsoleController extends BaseAdminController
{
    /**
     * @var string the name of the default action. Defaults to 'index'.
     */
    public $defaultAction = 'dashboard';
    
    /**
     * @see BaseAdminController::init()
     */
    public function init()
    {
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
                'actions' => array('index'),
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
        );
    }
}