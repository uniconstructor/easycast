<?php

// Подключаем родительский класс контроллера ВУЗа
Yii::import('questionary.controllers.QUniversityController');

/**
 * Контроллер для работы с музыкальными ВУЗами
 *
 * @package    easycast
 * @subpackage questionary
 */
class QMusicUniversityController extends QUniversityController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QMusicUniversity';
    
    /**
     * @see QUniversityController::actionGetUniversityList()
     */
    public function actionGetUniversityList()
    {
        parent::actionGetUniversityList();
    }
}