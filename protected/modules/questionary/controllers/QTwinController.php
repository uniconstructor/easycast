<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком дополнительных характеристик внешности
 *
 * @package    easycast
 * @subpackage questionary
 */
class QTwinController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QTwin';
}