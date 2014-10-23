<?php
/**
 * Шаблон с информацией о том как будут использованы введенные пользователем данные
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */
?>
<p class="note muted text-center">
    Вся введенная ниже информация будет видна только креативной группе при отборе заявок.
    <?= Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
</p>