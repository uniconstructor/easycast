<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

        <?php echo $form->label($model,'id'); ?>
        <?php echo $form->textField($model,'id'); ?>

        <?php echo $form->label($model,'username'); ?>
        <?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>128)); ?>

        <?php echo $form->label($model,'email'); ?>
        <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>

        <?php // echo $form->label($model,'create_at'); ?>
        <?php // echo $form->textField($model,'create_at'); ?>

        <?php // echo $form->label($model,'lastaccess'); ?>
        <?php // echo $form->textField($model,'lastaccess'); ?>

        <?php echo $form->label($model,'superuser'); ?>
        <?php echo $form->dropDownList($model,'superuser',$model->itemAlias('AdminStatus')); ?>

        <?php echo $form->label($model,'status'); ?>
        <?php echo $form->dropDownList($model,'status',$model->itemAlias('UserStatus')); ?>
        <br>
        <?php echo CHtml::submitButton(UserModule::t('Search'), array('class' => 'btn btn-primary')); ?>

<?php $this->endWidget(); ?>

</div><!-- search-form -->