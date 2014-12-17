<?php

/**
 * Новая версия админки: тема оформления smartAdmin, работает через AJAX
 */
class SmartAdminModule extends CWebModule
{
    /**
     * @var array
     */
    public $controllerMap = array(
        'ajax' => array(
            'class' => 'application.modules.smartAdmin.controllers.AjaxController',
        ),
    );
    /**
     * @var string the ID of the default controller for this module. Defaults to 'default'.
     */
    public $defaultController = 'ajax';
    /**
     * @var array - массив id фрагментов кода (клипов), для всплывающих форм сложных значений
     *              В форме анкеты требуется вывести множество "дочерних форм", а вкладывать их
     *              друг в друга нельзя поэтому выбрано такое решение
     *              Подробнее см. документацию класса CClipWidget
     */
    public $formClips = array();
    
    /**
     * @see CModule::init()
     */
	public function init()
	{
		$this->setImport(array(
		    'projects.models.*',
		    'questionary.models.*',
		    'smartAdmin.controllers.*',
		    'smartAdmin.controllers.actions.*',
		    // @todo удалить после замены модуля галереи
		    'ext.galleryManager.*',
		    'ext.galleryManager.components.*',
		    'ext.galleryManager.models.*',
		));
        // переопределяем базовые пакеты скриптов
        $corePackages   = require(YII_PATH.'/web/js/packages.php');
        $customPackages = array(
            'package-name' => array(
                'basePath'     => 'alias of the directory containing the script files',
                'baseUrl'      => 'base URL for the script files',
                // list of css files relative to basePath/baseUrl
                'js'           => array(''),
                // list of css files relative to basePath/baseUrl
                'css'          => array(''),
                // list of dependent packages
                'depends'      => array(),
            ),
        );
        // заменяем базовые скрипты и стили на собственные
        $packages = CMap::mergeArray($corePackages , $customPackages);
	}
    
	/**
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
        //throw new Exception('test');
        //die('kjhkj');
		if ( parent::beforeControllerAction($controller, $action) )
		{
            if( Yii::app()->user->isGuest === true )
            {// просим авторизоваться перед входом в админку
                Yii::app()->user->loginRequired();
            }
            if ( ! Yii::app()->user->checkAccess('Admin') )
            {// если нет прав доступа - делаем вид что админки здесь нет
                throw new CHttpException(404, 'Страница не найдена');
            }
			return true;
		}else
		{
		    return false;
		}
	}
	
	/**
	 * alias для функции получения строк перевода
	 * 
	 * @param  $str
	 * @param  $params
	 * @param  $dic
	 * @return string
	 */
	public static function t($str='', $params=array(), $dic='smartAdmin')
	{
	    if ( Yii::t("SmartAdminModule", $str) == $str )
	    {
	        return Yii::t("SmartAdminModule.".$dic, $str, $params);
	    }else
	    {
	        return Yii::t("SmartAdminModule", $str, $params);
	    }
	}
}