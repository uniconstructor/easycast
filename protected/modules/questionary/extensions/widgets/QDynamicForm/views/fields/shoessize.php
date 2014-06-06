<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// размер обуви
echo $form->dropDownListRow($model, 'shoessize', $model->questionary->getFieldVariants('shoessize'));