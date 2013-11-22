<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы с фильмографией
 * 
 * @package easycast
 * @subpackage questionary
 */
class QFilmInstanceController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QFilmInstance';
    
    /**
     * Создать запись в фильмографии
     * @return void
     */
    public function actionCreate()
    {
        $instance = new QFilmInstance();
        // id анкеты к которой добавляется фильмография
        $qid = Yii::app()->request->getParam('qid', 0);
        // ajax-проверка введенных данных
        $this->performAjaxValidation($instance);
        
        if ( $instanceData = Yii::app()->request->getPost('QFilmInstance') )
        {
            // проверяем права на добавление фильмографии к этой анкете
            $this->checkAccess($instance, $qid);
            
            $instance->attributes    = $instanceData;
            $instance->setName($instanceData['name']);
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
}