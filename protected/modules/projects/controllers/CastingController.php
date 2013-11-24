<?php

/**
 * Контроллер для работы с онлайн-кастингом
 * 
 * @package    easycast
 * @subpackage projects
 */
class CastingController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control
        );
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
                //'actions' => array('subscribe', 'selection'),
                'users'   => array('*'),
            ),
            /*array('allow',
                'actions' => array('accept', 'reject'),
                'users'   => array('@'),
            ),
            array('deny',  // запрещаем всё что явно не разрешено
                'users' => array('*'),
            ),*/
        );
    }
    
    /**
     * Создать онлайн-кастинг
     * 
     * @return void
     */
    public function actionCreate()
    {
        $model = new Project;
        $model->type = Project::TYPE_ONLINECASTING;
        
        if( $castingData = Yii::app()->request->getPost('Project') )
        {
            $model->attributes = $_POST[$castingData];
            if ( $model->save() )
            {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
        
        $this->render('create', array('model' => $model));
    }
    
    /**
     * Отобразить текущее состояние онлайн-кастинга
     *
     * @return void
     */
    public function actionView($id)
    {
        
    }
    
    /**
     * Добавить мероприятие в онлайн-кастинг
     * 
     * @return void
     */
    public function actionCreateEvent()
    {
        
    }
    
    /**
     * Создать роль для мероприятия в онлайн-кастинге
     * 
     * @return void
     */
    public function actionCreateRole()
    {
        
    }
    
    /**
     * Пометить кастинг как заполненный
     * 
     * @return void
     */
    public function actionMarkFilled()
    {
        
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * 
     * @param integer the ID of the model to be loaded
     * @return Project
     */
    public function loadModel($id)
    {
        $model = Project::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Страница не найдена');
        }elseif ( $model->type != Project::TYPE_ONLINECASTING )
        {
            throw new CHttpException(404, 'Страница не найдена');
        }
        return $model;
    }
}