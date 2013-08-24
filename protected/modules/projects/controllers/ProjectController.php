<?php

/**
 * Контроллер для работы с одним проектом. В основном используется для обработки AJAX-запросов.
 */
class ProjectController extends Controller
{
    /**
     * Отображение одного проекта
     * (редирект для совместимости)
     */
    public function actionView($id)
    {
        $this->redirect(Yii::app()->createUrl('/projects/projects/view', array('id' => $id)));
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