<?php
    $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
        'links'=>array_merge(
            $model->thread->getBreadcrumbs(true),
            array('Редактировать сообщение')
        ),
    ));
?>

<div class="form" style="margin:20px;">
    <?php 
        // begin edit post form
        $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'post-form',
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
        	    ),
            )); 
        // post content
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'content',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
        echo $form->error($model,'content'); 
     ?>
    <div class="form-actions">
    <?php 
        // submit button
        $this->widget('bootstrap.widgets.TbButton',
        array('buttonType' => 'submit',
              'type'        => 'primary',
              'label'       => Yii::t('coreMessages','save'),
              'htmlOptions' => array('id' => 'save_post')
        ));
    ?>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
