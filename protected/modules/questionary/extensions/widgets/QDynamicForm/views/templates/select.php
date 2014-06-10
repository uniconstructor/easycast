<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// select
$models = QActivityType::model()->forActivity($attribute)->findAll();
$options = CHtml::listData($models, 'id', 'translation');
echo $form->dropDownListRow($model, $attribute, $options);