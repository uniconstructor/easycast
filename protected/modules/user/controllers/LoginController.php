<?php

class LoginController extends Controller
{
	public $defaultAction = 'login';
	
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
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if ( Yii::app()->user->isGuest )
		{
			$model = new UserLogin;
			// collect user input data
			if( isset($_POST['UserLogin']) )
			{
				$model->attributes=$_POST['UserLogin'];
				// validate user input and redirect to previous page if valid
				if ( $model->validate() )
				{
					$this->lastVisit();
					if ( Yii::app()->user->returnUrl == '/index.php' )
					{
					    $this->redirect(Yii::app()->controller->module->returnUrl);
					}else
					{
					    $this->redirect(Yii::app()->user->returnUrl);
					}
				}
			}
			// display the login form
			$this->render('/user/login', array('model' => $model));
		}else
		{
		    $this->redirect(Yii::app()->controller->module->returnUrl);
		}
	}
	
	/**
	 * 
	 * @return void
	 */
	private function lastVisit()
	{
		$lastVisit = User::model()->notsafe()->findByPk(Yii::app()->user->id);
		$lastVisit->lastvisit = time();
		$lastVisit->save();
	}
}