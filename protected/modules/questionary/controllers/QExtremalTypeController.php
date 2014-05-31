<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком видов экстремального спорта
 *
 * @package    easycast
 * @subpackage questionary
 */
class QExtremalTypeController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QExtremalType';
}