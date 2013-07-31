<?php

/**
 * Класс для отображения информации о проекте пользователю
 * @todo удалить, действия просмотра, оставить только обработку AJAX-запросов
 */
class ProjectController extends Controller
{
    public function actionView($id)
    {
        $model = $this->loadModel($id);
        $this->render('view', array(
            'model' => $model,
            )
        );
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=Project::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }
}