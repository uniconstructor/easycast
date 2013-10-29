<?php 

/**
 * Поиск по разделам каталога
 * @todo выпадающее меню для поля visible
 * @todo отображать количество человек
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<?php echo $form->textFieldRow($model, 'name', array('class' => 'span5', 'maxlength'=>128)); ?>
	<?php echo $form->textFieldRow($model, 'order', array('class' => 'span5', 'maxlength'=>6)); ?>

	<?php // echo $form->textFieldRow($model,'count', array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->checkBoxRow($model,'visible', array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'       => 'primary',
			'label'      => 'Поиск',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
