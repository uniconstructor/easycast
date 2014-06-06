<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// рост
echo $form->textFieldRow($model, 'height', 
    array('style' => 'max-width:50px;', 'maxlength' => 6),
    array(
        'append' => Yii::t('coreMessages', 'cm'),
    )
);