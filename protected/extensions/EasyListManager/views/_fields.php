<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  ListItemsGrid */
/* @var $model EasyListItem */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));

// значение элемента списка
echo $form->textFieldRow($model, 'value', array(), array(
    'hint' => '',
));
// отображаемое название элемента
echo $form->textFieldRow($model, 'name', array(), array(
    'hint' => '',
));
if ( in_array('description', $this->fields) )
{// описание элемента
    echo $form->textAreaRow($model, 'description', array('style' => 'width:100%;'), array(
        'hint' => '',
    ));
}
// @todo модель значения
// @todo id значения
// @todo поле значения
//echo $form->dropDownListRow($model, 'type', $model->getTypeOptions());
