<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'qactivity-type-form',
    'enableAjaxValidation'=>false,
)); 

$model->name = $type;
$model->language = 'ru';
?>

    <?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php 
        echo $form->hiddenField($model,'name');
        echo $form->hiddenField($model,'language');
    ?>

    <?php echo $form->textFieldRow($model,'value',array('class'=>'span5','maxlength'=>32)); ?>

    <?php echo $form->textFieldRow($model,'translation',array('class'=>'span5','maxlength'=>255)); ?>

    <?php CHtml::hiddenField('class', $class) ?>
    <?php CHtml::hiddenField('type', $type) ?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
        )); ?>
    </div>

<?php $this->endWidget(); ?>