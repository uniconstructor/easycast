<?php

// Подключаем родительский класс контроллера ВУЗа
Yii::import('questionary.controllers.QUniversityController');

/**
 * Контроллер для работы с театральными ВУЗами
 * 
 * @package    easycast
 * @subpackage questionary
 */
class QActorUniversityController extends QUniversityController
{
    /**
     * @var string - класс модели сложного значения
     */
    public $modelClass = 'QActorUniversity';
    
    /**
     * @see QUniversityController::actionGetUniversityList()
     */
    public function actionGetUniversityList()
    {
        parent::actionGetUniversityList();
    }
}