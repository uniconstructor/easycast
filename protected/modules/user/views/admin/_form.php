<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array('enctype'=>'multipart/form-data'),
));
?>

	<p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary(array($model)); ?>

		<?php echo $form->textFieldRow($model,'email',array('size'=>60,'maxlength'=>128)); ?>

		<?php echo $form->dropDownListRow($model,'superuser',User::itemAlias('AdminStatus')); ?>

		<?php echo $form->dropDownListRow($model,'status',User::itemAlias('UserStatus')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',
            array('buttonType'=>'submit',
                  'type'=>'primary',
                  'label'=>$model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save'),
                  'htmlOptions'  => array('id' => 'save_questionary')
            )); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->