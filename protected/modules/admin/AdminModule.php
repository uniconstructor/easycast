<?php

/**
 * Админская часть сайта
 * @todo пускать в админку по ключу (только на определенные страницы)
 */
class AdminModule extends CWebModule
{
    /**
     * @var array
     */
    public $controllerMap = array(
        'fastOrder' => array(
            'class'=>'application.modules.admin.controllers.FastOrderController',
            ),
        );
    
    /**
     * (non-PHPdoc)
     * @see CModule::init()
     */
	public function init()
	{
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
    
	/**
	 * (non-PHPdoc)
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
		if( parent::beforeControllerAction($controller, $action) )
		{
    		$user = Yii::app()->getUser();
            if( $user->isGuest === true )
            {// просим авторизоваться перед входом в админку
                $user->loginRequired();
            }
            if ( ! Yii::app()->user->checkAccess('Admin') )
            {// если нет прав доступа - делаем вид что админки здесь нет
                throw new CHttpException(404, 'Страница не найдена');
                return false;
            }
			return true;
		}else
		{
		    return false;
		}
	}
	
	/**
	 * @param $str
	 * @param $params
	 * @param $dic
	 * @return string
	 */
	public static function t($str='', $params=array(), $dic='admin')
	{
	    if ( Yii::t("AdminModule", $str) == $str )
	    {
	        return Yii::t("AdminModule.".$dic, $str, $params);
	    }else
	    {
	        return Yii::t("AdminModule", $str, $params);
	    }
	}
}
