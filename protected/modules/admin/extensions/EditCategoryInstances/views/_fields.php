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

$form->hiddenField($model, 'objecttype');
$form->hiddenField($model, 'objectid');
// название
echo $form->dropDownList($model, 'categoryid', $this->getCategoryOptions());
