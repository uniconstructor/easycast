<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// hidden-поле для автоматически устанавливаемых значения
echo $form->hiddenField($model, $attribute);