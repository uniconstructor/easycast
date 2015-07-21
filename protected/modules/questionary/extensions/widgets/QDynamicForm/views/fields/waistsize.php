<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// талия
echo $form->textFieldRow($model, 'waistsize', 
    array('style' => 'max-width:50px;', 'maxlength' => 6),
    array(
        'append' => Yii::t('coreMessages', 'cm'),
    )
);