<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// отчество
echo $form->textFieldRow($model, 'middlename', 
    array(
        'size'        => 60,
        'maxlength'   => 128,
        'placeholder' => 'Отчество',
    ),
    array(
        'prepend'     => '<i class="icon icon-user"></i>',
    )
);