<?php

/**
 * Новая версия админки: тема оформления smartAdmin, работает через AJAX
 * 
 * @todo добавить новый параметр "isPageRequest" в класс CHttpRequest
 *       который отвечает за то, добавлять ли скрипты инициализации
 *       в конце запроса или нет
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
        'console' => array(
            'class' => 'application.modules.smartAdmin.controllers.ConsoleController',
        ),
    );
    /**
     * @var string the ID of the default controller for this module. Defaults to 'default'.
     */
    public $defaultController = 'console';
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
		    'smartAdmin.actions.*',
		    // @todo удалить после замены модуля галереи
		    'ext.galleryManager.*',
		    'ext.galleryManager.components.*',
		    'ext.galleryManager.models.*',
		));
        // @todo переопределяем базовые пакеты скриптов
        /*$corePackages   = require(YII_PATH.'/web/js/packages.php');
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
        $packages = CMap::mergeArray($corePackages , $customPackages);*/
        
        // в админке переключаемся на специальную тему оформления
        Yii::app()->setTheme('admin');
        // @todo убираем все скрипты и стили до тех пор пока не настроено подключение всех пакетов
        // через конфигурацию clientScript (скрипты yii все равно пока несовместимы с админской темой)
        // пока используем только то что подключается в layout
        Yii::app()->clientScript->enableJavaScript = false;
        // подключаем файлы скриптов в конце страницы для более стабильной работы 
        Yii::app()->clientScript->defaultScriptFilePosition = CClientScript::POS_END;
	}
    
	/**
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
		if ( parent::beforeControllerAction($controller, $action) )
	    {
	        if( Yii::app()->user->isGuest )
	        {// просим авторизоваться для использования любого действия в модуле управления правами
	            Yii::app()->user->loginRequired();
	        }
	        if ( Yii::app()->user->checkAccess('Admin') )
	        {
	            return true;
	        }else
	        {// без прав доступа делаем вид что такой страницы нет
	            throw new CHttpException(404, 'Страница не найдена');
	        }
	    }
	    return false;
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