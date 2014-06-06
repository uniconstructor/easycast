<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// имя
echo $form->textFieldRow($model, 'firstname', 
    array(
        'size'        => 60,
        'maxlength'   => 128,
        'placeholder' => 'Имя',
    ),
    array(
        'prepend'     => '<i class="icon icon-user"></i>',
    )
);