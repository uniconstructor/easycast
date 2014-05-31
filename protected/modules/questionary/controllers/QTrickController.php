<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком выполняемых трюков для каскадера
 *
 * @package    easycast
 * @subpackage questionary
 */
class QTrickController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QTrick';
}