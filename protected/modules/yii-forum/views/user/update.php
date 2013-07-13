<?php
$this->widget('bootstrap.widgets.TbBreadcrumbs', array('links'=>array(
    'Forum'=>array('/forum'),
    $model->name=>array('/forum/user/view', 'id'=>$model->id),
    'Update',
)));
?>

<div class="form" style="margin:20px;">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'forumuser-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
	),
    )); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'signature'); ?>
            <?php 
                $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
            	'model' => $model,
            	'attribute' => 'signature',
            	'options' => array(
            		'lang'    => 'ru',
                    ),
                ));
            ?>
            <?php echo $form->error($model,'signature'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Submit'); ?>
        </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
