<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы с мероприятиями ведущего
 * 
 * @package    easycast
 * @subpackage questionary
 */
class QEmceeController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QEmcee';
}