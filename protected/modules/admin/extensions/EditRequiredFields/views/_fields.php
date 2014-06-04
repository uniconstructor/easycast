<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditRequiredFields */
/* @var $model QUserField */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// тип и id для привязки нового поля 
echo CHtml::hiddenField('objectType', $this->objectType);
echo CHtml::hiddenField('objectId', $this->objectId);

// прикрепляемое поле
$options = $this->getFieldIdOptions();
echo $form->dropDownListRow($model, 'fieldid', $options, array(), array());
// галочка "обязательно к заполнению"
echo $form->widgetRow('ext.ECMarkup.ECToggleInput.ECToggleInput', 
    array(
        'model'     => $model,
        'attribute' => 'filling',
        'onLabel'   => 'Да',
        'onValue'   => 'required',
        'offLabel'  => 'Нет',
        'offValue'  => 'recommended',
    ),
    array(
        'hint' => 'При выборе "да" нельзя будет подать заявку без заполнения этого поля',
    )
);