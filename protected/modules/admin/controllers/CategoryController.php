<?php

/**
 * Контроллер для отображения списка категорий
 */
class CategoryController extends Controller
{
    public $layout = '//layouts/column2';
    
    /**
     * @return void
     */
    public function actionIndex()
    {
        $id = Yii::app()->request->getParam('parentId', 1);
        $this->render('index', array(
            'parentId' => $id,
        ));
    }
}