<?php

/**
 * Контроллер для работы с одним проектом. В основном используется для обработки AJAX-запросов.
 */
class ProjectController extends Controller
{
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     */
    protected $defaultModelClass = 'Project';
    
    /**
     * @see parent::filters()
     * 
     * @return array action filters
     */
    public function filters()
    {
        $baseFilters = parent::filters();
	    $newFilters  = array(
	        'accessControl',
	        array(
	            'ext.bootstrap.filters.BootstrapFilter',
	        ),
	    );
	    return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * 
     * @return array access control rules
     *
     * @todo добавить работу с правами через RBAC
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('view', 'ajaxInfo', 'banner'),
                'users'   => array('*'),
            ),
            array('deny',  // запрещаем всё что явно не разрешено
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * Отображение одного проекта (оставлено для совместимости)
     * 
     * @return void
     */
    public function actionView($id)
    {
        $this->redirect(Yii::app()->createUrl('/projects/projects/view', array('id' => $id)));
    }
    
    /**
     * Получить информацию о проекте при помощи AJAX-запроса
     * 
     * @return void
     */
    public function actionAjaxInfo()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(400, 'Only AJAX request allowed');
        }
        $id      = Yii::app()->request->getPost('id', 0);
        $project = $this->loadModel($id);
        
        $this->widget('projects.extensions.AjaxProjectInfo.AjaxProjectInfo', array(
            'project' => $project,
        ));
    }
    
    /**
     * Вывести файл баннера проекта с хранилища Amazon используя web-сервер easyCast как прокси
     * Эта функция нужна для того чтобы корректно работали кнопки репоста в соцсети
     * Если на странице есть изображения, которые грузятся с другого домена (не easycast.ru)
     * то они не будут отображаються в новости 
     * 
     * @return void
     */
    public function actionBanner()
    {
        $id      = Yii::app()->request->getParam('id', 0);
        $project = $this->loadModel($id);
        
        $bannerUrl = $project->getConfig('banner');
        $banner    = $project->getConfigValueObject('banner');
        
        if ( is_object($banner) AND $bannerUrl )
        {
            header('Content-Type: '.$banner->mimetype);
            echo Yii::app()->curl->get($bannerUrl);
        }
    }
    
    /**
	 * Получить модель проекта или отобразить сообщение о том что проект не найден
	 * @param int $id
	 * @throws CHttpException
	 * @return Project
	 */
    /*public function loadModel($id)
    {
        $model = Project::model()->findByPk($id);
	    if ( $model === null )
	    {
	        throw new CHttpException(404, 'Проект не найден');
	    }
	    return $model;
    }*/
}