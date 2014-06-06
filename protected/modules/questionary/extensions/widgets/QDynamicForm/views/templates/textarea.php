<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// многострочное текстовое поле
echo $form->textAreaRow($model, $attribute, $htmlOptions, $rowOptions);