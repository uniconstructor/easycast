<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// фамилия
echo $form->textFieldRow($model, 'lastname', 
    array(
        'size'        => 60,
        'maxlength'   => 128,
        'placeholder' => 'Фамилия',
    ),
    array(
        'prepend'     => '<i class="icon icon-user"></i>',
    )
);