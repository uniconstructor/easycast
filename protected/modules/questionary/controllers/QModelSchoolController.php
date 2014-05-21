<?php

// Подключаем родительский класс контроллера сложных значений
Yii::import('questionary.controllers.QComplexValueController');

/**
 * Контроллер для работы со списком модельных школ
 *
 * @package    easycast
 * @subpackage questionary
 */
class QModelSchoolController extends QComplexValueController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'QModelSchool';
}