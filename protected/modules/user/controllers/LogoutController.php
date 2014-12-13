<?php

class LogoutController extends Controller
{
	public $defaultAction = 'logout';
	
	/**
	 * @return array
	 *
	 * @todo настроить проверку прав на основе RBAC
	 */
	public function filters()
	{
	    $baseFilters = parent::filters();
	    $newFilters  = array(
	        // фильтр для подключения YiiBooster 3.x (bootstrap 2.x)
	        array(
	            'ext.bootstrap.filters.BootstrapFilter',
	        ),
	    );
	    return CMap::mergeArray($baseFilters, $newFilters);
	}
	
	/**
	 * Logout the current user and redirect to returnLogoutUrl.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->controller->module->returnLogoutUrl);
	}
}