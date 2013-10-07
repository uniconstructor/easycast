<?php 
/**
 * Форма создания/редактирования приглашения для заказчика
 * @todo сделать 3 кнопки "создать", "сохранить" и "отправить"
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'customer-invite-form',
	'enableAjaxValidation' => false,
));


?>
	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model, 'email',array('class'=>'span5','maxlength'=>255)); ?>
	<?php echo $form->textFieldRow($model, 'name',array('class'=>'span5','maxlength'=>255)); ?>
	<?php echo $form->textFieldRow($model, 'comment', array('class'=>'span5','maxlength'=>4095)); ?>

	<div class="form-actions">
		<?php
		if ( $model->isNewRecord )
		{
		    $this->widget('bootstrap.widgets.TbButton', array(
		        'buttonType' => 'submit',
		        'type'  => 'primary',
		        'label' => 'Отправить',
		    ));
		} 
        ?>
	</div>

<?php $this->endWidget(); ?>
