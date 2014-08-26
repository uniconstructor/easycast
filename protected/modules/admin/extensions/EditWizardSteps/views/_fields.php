<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditWizardSteps */
/* @var $model WizardStep */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// для автоматической привязки шага при создании
echo CHtml::hiddenField('objectType', $this->objectType);
echo CHtml::hiddenField('objectId', $this->objectId);
// название
echo $form->textFieldRow($model, 'name');
// заголовок
echo $form->textFieldRow($model, 'header');
// описание
echo $form->textAreaRow($model, 'description');
// текст кнопки "назад"
echo $form->textFieldRow($model, 'prevlabel');
// текст кнопки "вперед"
echo $form->textFieldRow($model, 'nextlabel');