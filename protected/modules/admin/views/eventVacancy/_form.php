<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'event-vacancy-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php 
	echo $form->labelEx($model,'description'); 
    $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'description',
    	'options' => array(
    		'lang' => 'ru',
            ),
    ));
    echo $form->error($model,'description');
    ?>

	<?php echo $form->textFieldRow($model,'limit',array('class'=>'span5','maxlength'=>6)); ?>
	
	<?php echo $form->textFieldRow($model,'salary',array('class'=>'span5','maxlength'=>7)); ?>
    <br>
	<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'primary',
		'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
	)); ?>

<?php $this->endWidget(); ?>
