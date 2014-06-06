<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// email
echo $form->textFieldRow($model, 'email',
    array(
        'size'        => 60,
        'maxlength'   => 255,
        'placeholder' => 'mail@example.com',
    ),
    array(
        'prepend'     => '<i class="icon icon-envelope"></i>',
    )
);