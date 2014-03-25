<?php 
/**
 * Форма создания вакансии (роли)
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'                   => 'event-vacancy-form',
	'enableAjaxValidation' => false,
)); 
?>
	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->textFieldRow($model, 'name', array('maxlength'=>255)); ?>
	<?php // описание роли
	echo $form->labelEx($model,'description'); 
    $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model'     => $model,
    	'attribute' => 'description',
    	'options'   => array('lang' => 'ru'),
    ));
    echo $form->error($model,'description');
    ?>
	<?php echo $form->textFieldRow($model, 'limit', array('maxlength'=>6)); ?>
	<?php echo $form->textFieldRow($model, 'salary', array('maxlength'=>7)); ?>
    <br>
	<?php 
	$this->widget('bootstrap.widgets.TbButton', array(
		'buttonType' => 'submit',
		'type'       => 'primary',
		'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить',
	)); 
	?>

<?php $this->endWidget(); ?>
