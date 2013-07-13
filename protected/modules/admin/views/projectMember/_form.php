<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'project-member-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'memberid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'vacancyid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'timecreated',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'timemodified',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'managerid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'request',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'responce',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'timestart',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'timeend',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'status',array('class'=>'span5','maxlength'=>9)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
