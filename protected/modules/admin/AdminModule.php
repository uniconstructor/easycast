<?php

/**
 * Админская часть сайта
 */
class AdminModule extends CWebModule
{
    public $controllerMap = array(
        'fastOrder' => array(
            'class'=>'application.modules.admin.controllers.FastOrderController',
            ),
        );
    
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'admin.models.*',
			'admin.components.*',
		    // Подключаем модели проекта - чтобы можно было работать с событиями и проектами
		    'projects.models.*',
		    
		    // Подключаем загрузку изображений
		    'ext.galleryManager.*',
		    'ext.galleryManager.models.*',
		));
		
		$this->defaultController = 'admin';
	}

	public function beforeControllerAction($controller, $action)
	{
	    if ( ! Yii::app()->user->isSuperuser )
	    {
	        throw new CHttpException(404,'Страница не найдена');
	    }
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
