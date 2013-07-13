<?php
    if(isset($forum)) $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
        'links'=>array_merge(
            $forum->getBreadcrumbs(true),
            array('Новая тема')
        ),
    ));
    else $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
        'links'=>array_merge(
            $thread->getBreadcrumbs(true),
            array('Ответить')
        ),
    ));
?>

<div class="form" style="margin:20px;">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'post-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
	),
    )); ?>

    <?php if(isset($forum)): ?>
        <div class="row">
            <?php echo $form->labelEx($model,'subject'); ?>
            <?php echo $form->textField($model,'subject'); ?>
            <?php echo $form->error($model,'subject'); ?>
        </div>
    <?php endif; ?>

        <div class="row">
            <?php echo $form->labelEx($model,'content'); ?>
            <?php 
                $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
            	'model' => $model,
            	'attribute' => 'content',
            	'options' => array(
            		'lang'    => 'ru',
                    ),
                ));
            ?>
            <?php echo $form->error($model,'content'); ?>
        </div>

        <?php if(Yii::app()->user->isAdmin): ?>
            <div class="row rememberMe">
                <?php echo $form->checkBox($model,'lockthread', array('uncheckValue'=>0)); ?>
                <?php echo $form->labelEx($model,'lockthread'); ?>
                <?php // echo $form->error($model,'lockthread'); ?>
            </div>
        <?php endif; ?>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Submit'); ?>
        </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
