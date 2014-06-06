<?php
/**
 * Разметка для динамической формы анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */
?>
<div class="row-fluid">
    <div class="offset2 span8">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'dynamic-registration-form',
        'enableAjaxValidation'   => true,
        'enableClientValidation' => false,
        'type'                   => 'horizontal',
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => false,
        ),
    ));
    ?>
    <p class="note muted" style="text-align:center;">
        <?= Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
    <?php 
    foreach ( $model->userFields as $userField )
    {// все поля формы (оставлены только нужные)
        echo $this->getUserFieldLayout($form, $model, $userField);
    }
    ?>
    <div class="title-page">
        <h4 class="intro-description">
            Вся введенная ниже информация будет видна только администраторам при отборе заявок.
        </h4>
    </div>
    <?php 
    foreach ( $model->extraFields as $extraField )
    {// все поля формы (оставлены только нужные)
        echo $this->getExtraFieldLayout($form, $model, $extraField);
    }
    
    ?>
    <div class="form-actions">
        <?php 
        // ошибки формы
        echo $form->errorSummary($model);
        // кнопка регистрации
        $form->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type'       => 'success',
            'size'       => 'large',
            'label'      => 'Продолжить'
        )); 
        ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
</div>