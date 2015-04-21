<?php

/**
 * Действие для работы с модулем cockpit
 */
class CockpitAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        define('COCKPIT_ADMIN', 1);
        define('COCKPIT_ADMIN_ROUTE', '/smartAdmin/console/cockpit');
        set_include_path(get_include_path().PATH_SEPARATOR.Yii::app()->basePath.'/../cockpit/vendor');
        include(Yii::app()->basePath.'/../cockpit/bootstrap.php');
        //$this->controller->renderPartial('index');
        //$this->controller->renderPartial('cockpit');
        //$this->controller->render('cockpit');
        // run backend
        $cockpit->set('route', COCKPIT_ADMIN_ROUTE)->trigger("admin.init")->run();
    }
}
