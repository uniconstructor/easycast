<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком типов вокала, которыми владеет пользователь
 *
 * @package    easycast
 * @subpackage questionary
 */
class QVocalTypeController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QVocalType';
}