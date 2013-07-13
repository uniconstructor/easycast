<?php

/**
 * Модуль "Галерея"
 */
class PhotosModule extends CWebModule
{
    /**
     * @var photosController
     */
    public $defaultController = 'photos';
    
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'photos.models.*',
		    'photos.controllers.*',
		    
		    'ext.galleryManager.*',
		    'ext.galleryManager.models.*',
		));
	}
	
    /**
     * (non-PHPdoc)
     * @see CWebModule::beforeControllerAction()
     */
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
	/**
	 * @param $str - id строки перевода
	 * @param $params - дополнительные параметры для строки
	 * @param $dic - используемый словарь
	 * @return string - переведенная строка
	 */
	public static function t($str='',$params=array(),$dic='photos') {
	    if (Yii::t("PhotosModule", $str)==$str)
	    {
	        return Yii::t("PhotosModule.".$dic, $str, $params);
	    }else
	    {
	        return Yii::t("PhotosModule", $str, $params);
	    }
	}
}
