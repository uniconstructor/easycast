<?php

/**
 * Базовый класс контроллера для работы со сложными значениями
 * 
 * @package easycast
 * @subpackage questionary
 */
class QComplexValueController extends Controller
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass;
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('questionary.models.*');
        Yii::import('questionary.models.complexValues.*');
        
        if ( ! $this->modelClass )
        {
            throw new CException('Не указан класс модели для контроллера сложных значений');
        }
    }
    
    /**
     * @return array action filters
     * @todo настроить проверку прав на основе RBAC
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }
    
    /**
     * @todo настроить доступ на основе ролей
     *
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'delete'),
                'users'   => array('@'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * Создать запись в фильмографии
     * @return void
     */
    public function actionCreate()
    {
        $modelClass = $this->modelClass;
        $instance = new $modelClass();
        // id анкеты к которой добавляется фильмография
        $qid = Yii::app()->request->getParam('qid', 0);
        // ajax-проверка введенных данных
        $this->performAjaxValidation($instance);
        
        if ( $instanceData = Yii::app()->request->getPost($this->modelClass) )
        {
            // проверяем права на добавление фильмографии к этой анкете
            $this->checkAccess($instance, $qid);
            
            $instance->attributes    = $instanceData;
            $instance->questionaryid = $qid;
            if ( ! $instance->save() )
            {
                throw new CHttpException(500, 'Ошибка при сохранении данных');
            }else
            {
                echo CJSON::encode($instanceData);
            }
        }
        Yii::app()->end();
    }
    
    /**
     * Обновить запись в фильмографии
     * @return void
     * 
     * @todo возвращать ошибки, связанные с другими полями
     */
    public function actionUpdate()
    {
        $id    = Yii::app()->request->getParam('pk');
        $field = Yii::app()->request->getParam('name');
        $value = Yii::app()->request->getParam('value');
        
        $item  = $this->loadModel($id);
        $this->checkAccess($item);
        
        $item->$field = $value;
        
        if ( ! $item->save() )
        {// не удалось обновить запись в поле
            throw new CHttpException(500, $item->getError($item->getRealFieldName($field)));
            die;
        }
    }
    
    /**
     * Удалить запись из фильмографии
     * @return void
     */
    public function actionDelete()
    {
        $id   = Yii::app()->request->getParam('id');
        $item = $this->loadModel($id);
        
        // проверяем права доступа к объекту
        $this->checkAccess($item);
        
        if ( ! $item->delete() )
        {
            throw new CHttpException(500, 'Ошибка при удалении записи');
            die;
        }
        
        echo $id;
    }
    
    /**
     * 
     * @param CActiveRecord $item
     * @return void
     */
    protected function checkAccess($item, $itemQuestionaryId=null)
    {
        $currentQuestionaryId = Yii::app()->getModule('questionary')->getCurrentUserQuestionaryId();
        if ( ! $itemQuestionaryId )
        {
            $itemQuestionaryId = $item->questionaryid;
        }
        if ( $currentQuestionaryId != $itemQuestionaryId AND ! Yii::app()->user->checkAccess('Admin') )
        {// нет прав на удаление записи
            throw new CHttpException(500, 'Ошибка при удалении записи: неверно указан id анкеты');
            die;
        }
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @todo корректно обработать случай когда мы не нашли данные
     *
     * @param integer the ID of the model to be loaded
     * @return CActiveRecord
     */
    public function loadModel($id)
    {
        $modelClass = $this->modelClass;
        $model = $modelClass::model($modelClass)->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Запись не найдена. (id='.$id.')');
        }
        return $model;
    }
    
    /**
     * Performs the AJAX validation.
     * @param CActiveRecord $model the model to be validated
     * 
     * @todo придумать более цивилизованный способ сообщать об ошибках
     */
    protected function performAjaxValidation($model)
    {
        if ( ! Yii::app()->request->getPost($this->modelClass) )
        {
            Yii::app()->end();
        }
        
        $result = CActiveForm::validate($model);
        if ( $result != '[]' )
        {// при сохранении обнаружены ошибки
            $errors = array();
            $result = CJSON::decode($result);
            foreach ($result as $element )
            {
                $errors[] = current($element);
            }
            $message = "\n".implode("\n", $errors);
            throw new CHttpException(400, $message);
            Yii::app()->end();
        }
	}
}