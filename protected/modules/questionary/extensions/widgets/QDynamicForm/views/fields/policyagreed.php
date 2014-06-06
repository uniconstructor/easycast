<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// согласие с условиями использования
echo $form->checkBoxRow($model, 'policyagreed');