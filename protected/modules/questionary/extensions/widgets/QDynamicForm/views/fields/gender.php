<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// пол
echo $form->widgetRow('ext.ECMarkup.ECToggleInput.ECToggleInput', array(
    'model'     => $model,
    'attribute' => 'gender',
    'onLabel'   => 'Мужской',
    'onValue'   => 'male',
    'offLabel'  => 'Женский',
    'offValue'  => 'female',
));