<?php

/**
 * Контроллер для работы со страницами новой админки
 * Вся новая админка работает через AJAX, поэтому ознакомьтесь с документацией 
 * админской темы прежде чем создавать новые страницы
 */
class AjaxController extends SmartAdminController
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
                'actions' => array('dashboard'),
                'roles'   => array('Admin'),
            ),
            array('allow',
                'actions' => array('selection'),
                'roles'   => array('Admin', 'Customer'),
            ),
            array('allow',
                'actions' => array('upload'),
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
    /*public function actions()
    {
        return array(
            'upload' => array(
                'class' => 'xupload.actions.S3XUploadAction',
            ),
        );
    }*/
    
    /**
     * @see CController::beforeAction()
     */
    /*public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }*/
    
    /**
     * @see CController::afterAction()
     */
    /*public function afterAction($action)
    {
        parent::afterAction($action);
    }*/
}