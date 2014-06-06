<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// дата через TbDatePicker
$formData = Yii::app()->request->getParam('QDynamicFormModel');
if ( isset($formData[$attribute]) )
{
    $model->$attribute = $formData[$attribute];
}elseif ( $model->$attribute )
{
    $model->$attribute = date(Yii::app()->params['outputDateFormat'], (int)$model->$attribute);
}else
{
    $model->$attribute = '';
}
echo $form->datePickerRow($model, $attribute, $htmlOptions, $rowOptions);