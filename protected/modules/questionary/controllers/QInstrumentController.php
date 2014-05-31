<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для редактирования списка музыкальных инструментов
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