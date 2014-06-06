<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// любое текстовое поле
echo $form->textFieldRow($model, $attribute, $htmlOptions, $rowOptions);