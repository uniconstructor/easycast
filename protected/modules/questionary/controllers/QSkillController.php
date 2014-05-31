<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком дополнительных умений и навыков
 *
 * @package    easycast
 * @subpackage questionary
 */
class QSkillController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QSkill';
}