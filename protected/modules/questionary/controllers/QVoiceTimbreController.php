<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком тембров голоса, которыми владеет пользователь
 *
 * @package    easycast
 * @subpackage questionary
 */
class QVoiceTimbreController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QVoiceTimbre';
}