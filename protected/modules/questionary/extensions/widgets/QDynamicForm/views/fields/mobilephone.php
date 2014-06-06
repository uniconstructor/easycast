<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// телефон
echo $form->textFieldRow($model, 'mobilephone',
    array(
        'size'        => 60,
        'maxlength'   => 20,
        'placeholder' => '(987)654-32-10',
    ),
    array(
        'prepend'     => '+7',
        'hint'        => 'Эта информация будет видна только администраторам. <br>Формат номера - 10 цифр.',
    )
);