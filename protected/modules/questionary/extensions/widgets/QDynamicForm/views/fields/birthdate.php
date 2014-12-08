<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

$dateFormat = Yii::app()->params['yiiDateFormat'];
// дата рождения
echo $form->datePickerRow($model, 'birthdate', array(
        'options' => array(
            'language'  => 'ru',
            'format'    => Yii::app()->params['inputDateFormat'],
            'startView' => 'decade',
            'weekStart' => 1,
            'startDate' => '-75y',
            'endDate'   => '-1y',
            'autoclose' => true,
        ),
        'htmlOptions' => array(
            'value' => Yii::app()->dateFormatter->format($dateFormat, $model->birthdate),
        ),
    ),
    array(
        'hint'    => 'Нажмите на название месяца или на год',
        'prepend' => '<i class="icon-calendar"></i>',
    )
);