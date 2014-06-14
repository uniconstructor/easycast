<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditCategories */
/* @var $model Categories */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// родительская категория
echo CHtml::hiddenField('parentid', $this->parentId);
// название
echo $form->textFieldRow($model, 'name');
// описание
echo $form->textFieldRow($model, 'description');