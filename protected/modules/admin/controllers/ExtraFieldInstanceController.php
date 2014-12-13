<?php

/**
 * Контроллер для редактирования списка дополнительных полей, привязанных к объекту
 * 
 * @todo вынести контроллеры такого типа в отдельный класс и поместить их в плагине EditableGrid
 */
class ExtraFieldInstanceController extends Controller
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'ExtraFieldInstance';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        parent::init();
        
        Yii::import('projects.models.*');
    }

    /**
     * @return array action filters
     * @todo настроить проверку прав на основе RBAC
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
                'actions' => array('create', 'update', 'delete', 'toggle', 'sortable'),
                'users'   => array('@'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * @see CController::actions()
     */
    public function actions()
    {
        return array(
            'toggle' => array(
                'class'     => 'bootstrap.actions.TbToggleAction',
                'modelName' => $this->modelClass,
            ),
            'sortable' => array(
                'class'      => 'bootstrap.actions.TbSortableAction',
                'modelName'  => $this->modelClass,
            ),
        );
    }

    /**
     * Создать запись
     * @return void
     */
    public function actionCreate()
    {
        $stepInstanceId = Yii::app()->request->getParam('wizardStepInstanceId');
        // создаем модель для добавления
        $instance = $this->initModel();
        
        // ajax-проверка введенных данных
        $this->performAjaxValidation($instance);

        if ( $instanceData = Yii::app()->request->getPost($this->modelClass) )
        {
            // проверяем права на добавление данных в эту анкету
            $this->checkAccess($instance);
            // привязываем значение к родительскому объекту (в этом случае - анкета)
            $instance->attributes = $instanceData;
            
            if ( ! $instance->save() )
            {
                throw new CHttpException(500, 'Ошибка при сохранении данных');
            }else
            {
                echo CJSON::encode($instance->getAttributes());
            }
        }
        if ( $stepInstanceId )
        {// если поле нужно добавить на определенном этапе регистрации - привязываем его
            $stepInstance = new ExtraFieldInstance();
            $stepInstance->objecttype = 'wizardstepinstance';
            $stepInstance->objectid   = $stepInstanceId;
            $stepInstance->fieldid    = $instance->fieldid;
            $stepInstance->save();
        }
    }

    /**
     * Обновить запись
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
            throw new CHttpException(500, $item->getError($field));
        }
    }

    /**
     * Удалить запись
     * @return void
     */
    public function actionDelete()
    {
        $id   = Yii::app()->request->getParam('id');
        $item = $this->loadModel($id);
        // проверяем права доступа к объекту
        $this->checkAccess($item);

        if ( ! $item->delete() )
        {// ошибка при удалении записи
            throw new CHttpException(500, 'Ошибка при удалении записи');
        }
        echo $id;
    }

    /**
     * Проверить, есть ли у пользователя доступ к добавлению, редактированию или удалению объекта
     * @param CActiveRecord $item
     * @return void
     */
    protected function checkAccess($item)
    {
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {
            throw new CHttpException(400, 'Ошибка при изменении записи');
        }
    }

    /**
     * Получить id "родительского" объекта - того объекта, к которому принадлежит редактируемая модель
     * @param CActiveRecord $item
     * @param string $parentObjectType - тип родительского объекта (к чему привязана модель?)
     * @return int
     *
     * @todo сделать более продуманный алгоритм определения родительского объекта: использовать
     *       $this->modelClass вместе с $parentObjectType
     */
    protected function getParentObjectId($item, $parentObjectType='vacancy')
    {
        return $item->objectid;
    }

    /**
     * Создает пустую модель нужного класса перед сохранением и привязкой к анкете
     * @return ExtraFieldInstance
     */
    protected function initModel()
    {
        return new $this->modelClass;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @todo корректно обработать случай когда мы не нашли данные
     *
     * @param integer the ID of the model to be loaded
     * @return ExtraFieldInstance
     */
    public function loadModel($id, $modelClass='')
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
        }
    }
}