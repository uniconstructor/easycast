<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditCategoryInstances */
/* @var $model CategoryInstance */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// скрытые поля для модели
echo $form->hiddenField($model, 'objecttype');
echo $form->hiddenField($model, 'objectid');
// название
echo $form->dropDownList($model, 'categoryid', $this->getCategoryOptions());
