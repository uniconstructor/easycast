<?php 

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы с фильмографией
 * 
 * @package easycast
 * 
 * @todo добавить возможность использовать этот контроллер не только для видео в анкете 
 *       но и для других объектов
 * @todo перенести добавить дополнительный уровен абстракции: класс BaseGridController
 *       и наследовать от него QComplexValueController
 * @todo добавить возможность добавлять видео незарегистрированным пользователям
 *      (только если это понадобится в форме быстрой регистрации)
 */
class VideoController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'Video';
    /**
     * @var sting - тип объекта которому по умолчанию принадлежит видео
     */
    protected $objectType = 'questionary';
    
    /**
     * @see QComplexValueController::initModel()
     * @return Video
     */
    protected function initModel()
    {
        $modelClass = $this->modelClass;
        $model = new $modelClass();
        $model->objecttype = $this->objectType;
        
        return $model;
    }
    
    /**
     * Создать запись
     * @return void
     */
    public function actionCreate()
    {
        $instance = $this->initModel();
        // id анкеты к которой добавляется фильмография
        $qid = Yii::app()->request->getParam('qid', 0);
        // ajax-проверка введенных данных
        $this->performAjaxValidation($instance);
    
        if ( $instanceData = Yii::app()->request->getPost($this->modelClass) )
        {
            // проверяем права на добавление фильмографии к этой анкете
            $this->checkAccess($instance, $qid);
            // привязываем видео к модели
            $instance->attributes = $instanceData;
            $instance->objectid   = $qid;
            if ( ! $instance->save() )
            {
                throw new CHttpException(500, 'Ошибка при сохранении данных');
            }else
            {
                echo CJSON::encode($instance->getAttributes());
            }
        }
        Yii::app()->end();
    }
}