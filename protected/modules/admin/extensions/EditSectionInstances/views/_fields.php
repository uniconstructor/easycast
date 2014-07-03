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
echo $form->hiddenField($model, 'objecttype');
echo $form->hiddenField($model, 'objectid');
// название
echo $form->dropDownListRow($model, 'sectionid', $this->getSectionOptions());
// видимость
echo $form->dropDownListRow($model, 'visible', array(
    '1' => Yii::t('coreMessages', '1'), 
    '0' => Yii::t('coreMessages', '0'),
));
