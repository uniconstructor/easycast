<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditModelSchools */
/* @var $model QModelSchool */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// название
echo $form->textFieldRow($model, 'name');
// год
echo $form->datepickerRow($model, 'year', array(
    'options' => $this->getYearPickerOptions(),
));