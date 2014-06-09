<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// согласие с условиями использования
// @todo придумать как можно переместить галочку в произвольное место формы
$model->policyagreed = 1;
echo $form->checkBoxRow($model, 'policyagreed');