<?php
    $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
        'links'=>array_merge(
            $model->getBreadcrumbs(true),
            array('Редактировать')
        ),
    ));
?>

<div class="form" style="margin:20px;">
    <?php
        // begin edit thread form 
        $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'thread-form',
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
        	),
        )); 
    
        // thread subject
        echo $form->textFieldRow($model,'subject');
        echo $form->error($model,'subject'); 

        // sticky thread? (yes/no)
        echo $form->checkBoxRow($model,'is_sticky',array('uncheckValue'=>0)); 
        // lock this thread? (yes/no)
        echo $form->checkBoxRow($model,'is_locked',array('uncheckValue'=>0));
    ?>

    <div class="row buttons">
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
