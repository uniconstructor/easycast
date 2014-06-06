<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// дата рождения
$formData = Yii::app()->request->getParam('QDynamicFormModel');
if ( isset($formData['birthdate']) )
{
    $model->birthdate = $formData['birthdate'];
}elseif ( $model->birthdate )
{
    $model->birthdate = date(Yii::app()->params['outputDateFormat'], (int)$model->birthdate);
}else
{
    $model->birthdate = '';
}
echo $form->datePickerRow($model, 'birthdate',
    array(
        'options' => array(
            'language'  => 'ru',
            'format'    => 'dd.mm.yyyy',
            'startView' => 'decade',
            'weekStart' => 1,
            'startDate' => '-75y',
            'endDate'   => '-1y',
            'autoclose' => true,
        ),
    ),
    array(
        'hint'    => 'Нажмите на название месяца или на год',
        'prepend' => '<i class="icon-calendar"></i>',
    )
);