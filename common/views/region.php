<?php

use yii\helpers\Html;

/**
 * Служебный view-файл для отображения одного региона cockpit
 * Используется когда нужно воспользоваться такими функциями контроллера 
 * как render() или renderPartial() - они требуют обязательного наличия view-файла
 */
/* @var $name string - название отображаемого региона (свойство name) */
/* @var $data array  - переменные подставляемые в разметку региона */

// выводим html региона - больше ничего не нужно
region($name, $data);