<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<?php echo $form->textFieldRow($model,'id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'projectid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'description',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'timestart',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'timeend',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'timecreated',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'timemodified',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'addressid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'status',array('class'=>'span5','maxlength'=>9)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
