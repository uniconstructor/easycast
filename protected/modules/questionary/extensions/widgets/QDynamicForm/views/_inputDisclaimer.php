<?php
/**
 * Шаблон с информацией о том как будут использованы введенные пользователем данные
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */
?>
<div class="title-page">
    <h4 class="intro-description">
        Вся введенная ниже информация будет видна только креативной группе при отборе заявок.
    </h4>
</div>
<p class="note muted text-center">
    <?= Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
</p>