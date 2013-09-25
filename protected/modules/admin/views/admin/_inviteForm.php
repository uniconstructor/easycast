<?php 
/**
 * Форма отправки одноразовой ссылки заказчику 
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'customer-invite-form',
    'enableAjaxValidation' => false,
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php // echo $form->textFieldRow($model,'objecttype',array('class'=>'span5','maxlength'=>11)); ?>

    <?php // echo $form->textFieldRow($model,'objectid',array('class'=>'span5','maxlength'=>11)); ?>

    <?php echo $form->textFieldRow($model, 'email', array('class' => 'span5','maxlength' => 255)); ?>

    <?php echo $form->textFieldRow($model, 'name', array('class' => 'span5','maxlength' => 255)); ?>

    <?php echo $form->textFieldRow($model, 'comment', array('class' => 'span5','maxlength' => 4095)); ?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type'  => 'primary',
            'label' => 'Отправить',
        )); ?>
    </div>

<?php $this->endWidget(); ?>