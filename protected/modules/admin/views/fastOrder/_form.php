<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'fast-order-form',
	'enableAjaxValidation'=>false,
)); 

?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<?php echo $form->uneditableRow($model,'name',array('class'=>'span5','maxlength'=>128, 'disabled')); ?>

	<?php echo $form->uneditableRow($model,'phone',array('class'=>'span5','maxlength'=>20, 'disabled')); ?>

	<?php echo $form->uneditableRow($model,'email',array('class'=>'span5','maxlength'=>255, 'disabled')); ?>

	<?php echo $form->uneditableRow($model,'comment',array('class'=>'span5','maxlength'=>255), 'disabled'); ?>
	
	<?php echo $form->dropDownListRow($model,'status', $model->getStatusVariants()); ?>

	<?php echo $form->textAreaRow($model,'ourcomment',array('class'=>'span5','maxlength'=>255)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=> 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
