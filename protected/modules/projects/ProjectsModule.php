<?php

/**
 * Модуль "проекты и мероприятия"
 */
class ProjectsModule extends CWebModule
{
    /**
     * @var ProjectsController
     */
    public $defaultController = 'projects';
    
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'projects.models.*',
		    'projects.controllers.*',
		    
		    'ext.galleryManager.*',
		    'ext.galleryManager.models.*',
		));
	}
	
	/**
	 * Получить условия для выбора проектов в списке
	 * @todo прописать критерий через SearchScopes
	 * @param array|SearchScope $scopes - критерий поиска анкет или несколько таких критериев поиска
	 *         (например если мы ищем по своим критериям внутри раздела каталога)
	 * @return CDbCriteria
	 */
	public function getProjectsCriteria($scopes=array())
	{
	    $criteria  = new CDbCriteria();
	    // Показываем в базе пользователей только проверенные анкеты
	    $criteria->compare('status', 'active');
	     
	    return $criteria;
	}

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
	public static function t($str='',$params=array(),$dic='projects') {
	    if (Yii::t("ProjectsModule", $str)==$str)
	    {
	        return Yii::t("ProjectsModule.".$dic, $str, $params);
	    }else
	    {
	        return Yii::t("ProjectsModule", $str, $params);
	    }
	}
}
