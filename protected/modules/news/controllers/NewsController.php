<?php
/**
 * Контроллер для отображения новостей пользователю
 */
class NewsController extends Controller
{
    //public $layout='//layouts/column1';

    /**
     * (non-PHPdoc)
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('news.models.News');
    }
    
    /**
     * Отображение всех новостей
     */
    public function actionIndex()
    {
        $this->render('/news/index');
    }
    
    /**
     * Просмотр одной новости
     * @param int $id - id новости
     */
    public function actionView($id)
    {
        $newsItem = $this->loadModel($id);
        
        $this->render('/news/view', array(
            'newsItem' => $newsItem,
        ));
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=News::model()->findByPk($id);
        if($model===null OR ! $model->visible )
            throw new CHttpException(404,'Новость не найдена');
        return $model;
    }
}