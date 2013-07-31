<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<?php echo $form->textFieldRow($model,'id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'parentid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'scopeid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textFieldRow($model,'shortname',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textFieldRow($model,'lang',array('class'=>'span5','maxlength'=>5)); ?>

	<?php echo $form->textFieldRow($model,'galleryid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'content',array('class'=>'span5','maxlength'=>8)); ?>

	<?php echo $form->textFieldRow($model,'order',array('class'=>'span5','maxlength'=>6)); ?>

	<?php echo $form->textFieldRow($model,'count',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->checkBoxRow($model,'visible',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
