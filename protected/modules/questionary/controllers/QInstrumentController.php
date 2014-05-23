<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком танцев
 *
 * @package    easycast
 * @subpackage questionary
*/
class QInstrumentController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QInstrument';
}