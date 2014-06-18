<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditExtraFieldInstances */
/* @var $model ExtraFieldInstance */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// тип и id для привязки нового поля 
echo $form->hiddenField($model, 'objecttype');
echo $form->hiddenField($model, 'objectid');

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
        'onId'      => $this->id.'_filling_on_button',
        'offId'     => $this->id.'_filling_off_button',
    ),
    array(
        'hint' => 'При выборе "да" нельзя будет подать заявку без заполнения этого поля',
    )
);
// значение по умолчанию
echo $form->textFieldRow($model, 'default', array(), array(
    'hint' => 'Это значение установится всем ранее подавшим заявку участникам если они
        подали заявку до того как поле было добавлено',
));