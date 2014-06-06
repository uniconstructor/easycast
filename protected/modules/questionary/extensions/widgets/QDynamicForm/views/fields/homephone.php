<?php
/**
 * Разметка одного поля анкеты
*/
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// телефон
echo $form->textFieldRow($model, 'homephone',
    array(
        'size'        => 60,
        'maxlength'   => 20,
        'placeholder' => '(495)444-44-44',
    ),
    array(
        'prepend'     => '+7',
        'hint'        => 'Эта информация будет видна только администраторам. <br>Формат номера - 10 цифр.',
    )
);