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
            'beforeValidate'   => "js:function(form){
                $('#dynamic-registration-submit_{$this->vacancy->id}').prop('disabled', 'disabled');
                $('#dynamic-registration-submit_{$this->vacancy->id}').removeClass('btn-success');
                $('#dynamic-registration-submit_{$this->vacancy->id}').addClass('btn-disabled');
                $('#dynamic-registration-submit_{$this->vacancy->id}').text('Проверка...');
                
                return true;
            }",
            'afterValidate'    => "js:function(form, data, hasError){
                $('#dynamic-registration-submit_{$this->vacancy->id}').removeProp('disabled');
                $('#dynamic-registration-submit_{$this->vacancy->id}').addClass('btn-success');
                $('#dynamic-registration-submit_{$this->vacancy->id}').removeClass('btn-disabled');
                $('#dynamic-registration-submit_{$this->vacancy->id}').text('Отправить');
                
                return ! hasError;
            }",
        ),
    ));
    // выводим специальный скрытый элемент, который каждую минуту посылает запрос на сайт, чтобы при длительном
    // заполнении анкеты не произошла потеря сессии и все данные не пропали
    $this->widget('ext.EHiddenKeepAlive.EHiddenKeepAlive', array(
        'url'    => Yii::app()->createAbsoluteUrl('//site/keepAlive'),
        'period' => 45,
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
        // @todo не выводится ошибка через beforeValidate
        echo $form->error($model, 'galleryid').'<br>';
        // кнопка регистрации
        $form->widget('bootstrap.widgets.TbButton', array(
            'id'         => 'dynamic-registration-submit_'.$this->vacancy->id,
            'buttonType' => 'submit',
            'type'       => 'success',
            'size'       => 'large',
            'label'      => 'Отправить',
        )); 
        ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
</div>