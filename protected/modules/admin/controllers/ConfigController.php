<?php

/**
 * Контроллер для работы с настройками в админке
 */
class ConfigController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     *      using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column1';
    /**
     * @var string - класс модели, по умолчанию используемый для метода $this->loadModel()
     */
    protected $defaultModelClass = 'Config';
    
    /**
     * @see CController::actions()
     */
    public function actions()
    {
        return array(
            // добавить элемент в список значений настройки
            'createValue' => array(
                'class'      => 'application.actions.config.EcCreateConfigValue',
                'modelName'  => 'EasyListItem',
            ),
            // редактировать значение настройки
            'updateValue' => array(
                'class'      => 'application.actions.config.EcUpdateConfigValue',
                'modelName'  => 'EasyListItem',
            ),
            // удалить элемент из списка значений настройки
            'deleteValue' => array(
                'class'      => 'application.actions.config.EcDeleteConfigValue',
                'modelName'  => 'EasyListItem',
            ),
            // редактировать данные самой настройки (модель Config)
            'updateConfig' => array(
                'class'      => 'application.actions.config.EcCreateConfigObject',
                'modelName'  => 'EasyListItem',
            ),
        );
    }
    
    /**
     * @return array action filters
     */
    public function filters()
    {
        $baseFilters = parent::filters();
        $newFilters  = array(
            'accessControl',
            'postOnly + delete',
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
     */
    public function accessRules()
    {
        return array(
            array('allow',
             'actions' => array('index', 'view', 'createValue', 'updateValue', 'deleteValue', 'deleteConfig'),
                'roles' => array('admin'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Все системные настройки
     */
    public function actionIndex()
    {
        $this->render('index');
    }
    
    /**
     * Настройки одной модели
     */
    public function actionView()
    {
        $id   = Yii::app()->request->getParam('id', 0);
        $type = Yii::app()->request->getParam('type', 'system');
        
        $this->render('view', array(
            'type' => $type,
            'id'   => $id,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * 
     * @param integer the ID of the model to be loaded
     */
    /*public function loadModel($id)
    {
        $model = Config::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $model;
    }*/

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if ( isset($_POST['ajax']) && $_POST['ajax'] === 'config-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}