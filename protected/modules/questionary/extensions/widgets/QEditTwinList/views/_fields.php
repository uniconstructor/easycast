<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditTwinList */
/* @var $model QTwin */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// название
echo $form->textFieldRow($model, 'name');
// доп. комментарий
echo $form->textAreaRow($model, 'comment', null, array(
    'hint' => 'Не обязательно',
));