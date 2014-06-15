<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditCatalogSections */
/* @var $model CatalogSection */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// категория
echo $form->hiddenField($model, 'categoryid');
// название
echo $form->textFieldRow($model, 'name');