<?php
/* @var $this AddressController */
/* @var $model Address */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'address-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'objecttype'); ?>
		<?php echo $form->textField($model,'objecttype',array('size'=>12,'maxlength'=>12)); ?>
		<?php echo $form->error($model,'objecttype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'objectid'); ?>
		<?php echo $form->textField($model,'objectid',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'objectid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'postalcode'); ?>
		<?php echo $form->textField($model,'postalcode',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'postalcode'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'country'); ?>
		<?php echo $form->textField($model,'country',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'country'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'region'); ?>
		<?php echo $form->textField($model,'region',array('size'=>6,'maxlength'=>6)); ?>
		<?php echo $form->error($model,'region'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'city'); ?>
		<?php echo $form->textField($model,'city',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'streettype'); ?>
		<?php echo $form->textField($model,'streettype',array('size'=>16,'maxlength'=>16)); ?>
		<?php echo $form->error($model,'streettype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'streetname'); ?>
		<?php echo $form->textField($model,'streetname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'streetname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'number'); ?>
		<?php echo $form->textField($model,'number',array('size'=>16,'maxlength'=>16)); ?>
		<?php echo $form->error($model,'number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'housing'); ?>
		<?php echo $form->textField($model,'housing',array('size'=>16,'maxlength'=>16)); ?>
		<?php echo $form->error($model,'housing'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'gate'); ?>
		<?php echo $form->textField($model,'gate',array('size'=>8,'maxlength'=>8)); ?>
		<?php echo $form->error($model,'gate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'floor'); ?>
		<?php echo $form->textField($model,'floor'); ?>
		<?php echo $form->error($model,'floor'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'apartment'); ?>
		<?php echo $form->textField($model,'apartment',array('size'=>16,'maxlength'=>16)); ?>
		<?php echo $form->error($model,'apartment'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'timecreated'); ?>
		<?php echo $form->textField($model,'timecreated',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'timecreated'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'timemodified'); ?>
		<?php echo $form->textField($model,'timemodified',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'timemodified'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'latitude'); ?>
		<?php echo $form->textField($model,'latitude'); ?>
		<?php echo $form->error($model,'latitude'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'longitude'); ?>
		<?php echo $form->textField($model,'longitude'); ?>
		<?php echo $form->error($model,'longitude'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status',array('size'=>7,'maxlength'=>7)); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'encrypted'); ?>
		<?php echo $form->textField($model,'encrypted'); ?>
		<?php echo $form->error($model,'encrypted'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->