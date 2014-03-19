<?php

/**
 * Контроллер для работы с одним проектом. В основном используется для обработки AJAX-запросов.
 */
class ProjectController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        $baseFilters = parent::filters();
	    $newFilters  = array(
	        'accessControl',
	    );
	    return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     *
     * @todo добавить работу с правами через RBAC
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('view', 'ajaxInfo'),
                'users'   => array('*'),
            ),
            array('deny',  // запрещаем всё что явно не разрешено
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * Отображение одного проекта
     * (оставлено для совместимости)
     * @return void
     */
    public function actionView($id)
    {
        $this->redirect(Yii::app()->createUrl('/projects/projects/view', array('id' => $id)));
    }
    
    /**
     * Получить информацию о проекте при помощи AJAX-запроса
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
	 * Получить модель проекта или отобразить сообщение о том что проект не найден
	 * @param int $id
	 * @throws CHttpException
	 * @return Project
	 */
    public function loadModel($id)
    {
        $model = Project::model()->findByPk($id);
	    if ( $model === null )
	    {
	        throw new CHttpException(404, 'Проект не найден');
	    }
	    return $model;
    }
}