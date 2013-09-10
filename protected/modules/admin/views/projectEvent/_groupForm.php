<?php 
/**
 * Форма редактирования группы мероприятий в админке
 * @todo добавить возможность задавать значения по умолчанию для группы
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id' => 'project-event-form',
	'enableAjaxValidation' => false,
)); 
?>
	<?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->textFieldRow($model, 'name', array('class'=>'span5', 'maxlength'=>255)); ?>
	
	<?php
	// тип (всегда группа)
	$model->type = 'group';
	echo $form->hiddenField($model, 'type');
	?>
	
	<?php 
	// описание для группы
	echo $form->labelEx($model,'description'); 
    $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'description',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
    echo $form->error($model,'description');
    ?>
	

	<?php // echo $form->textFieldRow($model,'addressid',array('class'=>'span5','maxlength'=>11)); ?>
	<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'primary',
		'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
	)); ?>

<?php $this->endWidget(); ?>
