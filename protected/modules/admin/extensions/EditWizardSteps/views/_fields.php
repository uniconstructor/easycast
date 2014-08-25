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
// для автоматической привязки шага к роли при создании
echo $form->hiddenField($model, 'objecttype');
echo $form->hiddenField($model, 'objectid');
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