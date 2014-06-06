<?php
/**
 * Разметка одного поля анкеты
*/
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// вес
echo $form->textFieldRow($model, 'weight', 
    array('style' => 'max-width:50px;', 'maxlength' => 6),
    array(
        'append' => Yii::t('coreMessages', 'kg'),
    )
);