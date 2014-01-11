<?php 

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы с видео: позволяет прикреплять видеоролики к любым моделям
 * 
 * @package easycast
 * 
 * @todo пока работает только с анкетой. Добавить возможность использовать этот контроллер 
 *       не только для видео в анкете но и для других объектов
 * @todo перенести добавить дополнительный уровень абстракции: класс BaseGridController
 *       и наследовать от него QComplexValueController
 * @todo добавить возможность добавлять видео незарегистрированным пользователям
 *      (только если это понадобится в форме быстрой регистрации)
 */
class VideoController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения формы: в этом контроллере значение всегда будет 'Video',
     *               само поле нужно для работы родительского класса
     * @see QComplexValueController::modelClass
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
     * Привязать видео к анкете
     * @return void
     */
    public function actionCreate()
    {
        $instance = $this->initModel();
        // id анкеты к которой добавляется видео
        $qid = Yii::app()->request->getParam('qid', 0);
        // ajax-проверка введенных данных
        $this->performAjaxValidation($instance);
    
        if ( $instanceData = Yii::app()->request->getPost($this->modelClass) )
        {// проверяем права на добавление фильмографии к этой анкете
            $this->checkAccess($instance, $qid);
            // привязываем видео к модели
            $instance->attributes = $instanceData;
            $instance->objectid   = $qid;
            if ( ! $instance->save() )
            {
                $errors = implode(', ', $instance->getErrors());
                throw new CHttpException(500, 'Ошибка при добавлении видео. '.$errors);
            }else
            {// при успешном сохранении видео - отдаем json с его данными, они могут понадобится для вывода
                // сообщения через AJAX
                echo CJSON::encode($instance->getAttributes());
            }
        }
        Yii::app()->end();
    }
}