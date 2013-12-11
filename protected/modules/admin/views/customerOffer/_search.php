<?php 
/**
 * @todo удалить, если не пригодится
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<?php echo $form->textFieldRow($model,'id',array('class'=>'span5')); ?>

	<?php // echo $form->textFieldRow($model,'objecttype',array('class'=>'span5','maxlength'=>11)); ?>

	<?php // echo $form->textFieldRow($model,'objectid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php // echo $form->textFieldRow($model,'key',array('class'=>'span5','maxlength'=>40)); ?>

	<?php // echo $form->textFieldRow($model,'key2',array('class'=>'span5','maxlength'=>40)); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php // echo $form->textFieldRow($model,'managerid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php // echo $form->textFieldRow($model,'timecreated',array('class'=>'span5','maxlength'=>11)); ?>

	<?php // echo $form->textFieldRow($model,'timeused',array('class'=>'span5','maxlength'=>11)); ?>

	<?php // echo $form->textFieldRow($model,'comment',array('class'=>'span5','maxlength'=>4095)); ?>

	<?php // echo $form->textFieldRow($model,'userid',array('class'=>'span5','maxlength'=>11)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Поиск',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
