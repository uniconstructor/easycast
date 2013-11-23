<?php

// Подключаем родительский класс контроллера ВУЗа
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы с театральными ВУЗами
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
}