<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы с показами модели
 *
 * @package    easycast
 * @subpackage questionary
 */
class QPromoModelJobController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QPromoModelJob';
}