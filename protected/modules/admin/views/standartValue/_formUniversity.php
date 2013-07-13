 <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'quniversity-form',
    'enableAjaxValidation'=>false,
));

$model->type = $type;
if ( $model->isNewRecord )
{
    $model->system = 1;
}
?>

    <?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php 
        echo $form->textFieldRow($model,'type',array('class'=>'span5','maxlength'=>7,'disabled'=>'disabled'));
        echo $form->hiddenField($model,'type');
    ?>

    <?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>128)); ?>

    <?php // echo $form->textFieldRow($model,'link',array('class'=>'span5','maxlength'=>255)); ?>

    <?php echo $form->checkBoxRow($model,'system',array('class'=>'span5')); ?>
    
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