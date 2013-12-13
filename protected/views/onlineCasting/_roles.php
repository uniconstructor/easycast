<?php
/**
 * Страница создания роли в онлайн-кастинге
 */

/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'online-casting-role-form',
    'enableAjaxValidation'   => true,
    'enableClientValidation' => true,
    'type' => 'horizontal',
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
    ),
    'action' => Yii::app()->createUrl('/onlineCasting/create'),
));

?>
<div id="wizard-bar" class="progress progress-striped">
    <div class="bar"></div>
</div>
<div class="span8 offset2">
    <h1 style="text-align:center;">Информация о кастинге</h1>
    <p class="note muted" style="text-align:center;">
        <?php echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
    </p>
    <?php 
    // ошибки формы
    echo $form->errorSummary($onlineCastingRoleForm);
    // роль
    echo $form->textFieldRow($onlineCastingRoleForm, 'name', array(
        'size'        => 60,
        'maxlength'   => 255,
        'prepend'     => '<i class="icon icon-user"></i>',
        'placeholder' => 'Имя'));
    // описание роли
    echo $form->redactorRow($onlineCastingRoleForm, 'description', array(
        'options' => array(
            'lang' => 'ru')
    ));
    
    ?>
    <input type="hidden" name="step" value="roles">
    <div class="form-actions">
        <?php 
        // кнопка отправки
        $form->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'submit',
                //'buttonType' => 'ajaxSubmit',
                'type'       => 'success',
                'size'       => 'large',
                'label'      => 'Следующий шаг >',
                /*'url'        => Yii::app()->createUrl('/onlineCasting/saveCasting', array(
                    'ajax' => 'online-casting-form')
                ),*/
                'htmlOptions' => array(
                    'class' => 'button-next',
                ),
                /*'ajaxOptions' => array(
                    'method' => 'post',
                ),*/
            )
        ); 
        ?>
    </div>
    <?php 
        $this->endWidget();
    ?>
</div>