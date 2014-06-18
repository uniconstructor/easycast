<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditSectionInstances */
/* @var $model CatalogSectionInstance */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// тип и id для привязки нового поля
echo CHtml::hiddenField('objectType', $this->objectType);
echo CHtml::hiddenField('objectId', $this->objectId);
// название
echo $form->dropDownList($model, 'sectionid', $this->getSectionOptions());
