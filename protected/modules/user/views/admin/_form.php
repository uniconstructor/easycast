<div class="form">

<?php 
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'                   => 'user-form',
	'enableAjaxValidation' => true,
	'htmlOptions'          => array('enctype'=>'multipart/form-data'),
));
?>
	<!--p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p-->
	<?php echo $form->errorSummary(array($model)); ?>
	<?php echo $form->textFieldRow($model, 'email', array('autocomplete' => 'off', 'maxlength' => 255)); ?>
	<?php echo $form->dropDownListRow($model, 'superuser', User::itemAlias('AdminStatus')); ?>
	<?php echo $form->dropDownListRow($model, 'status', User::itemAlias('UserStatus')); ?>
	<?php // источник данных (или "владелец анкеты"). Кого указать в качестве "автора" анкеты
        // 823 - это номер пользователя Светланы (стоит здесь по умолчанию на время ввода ее базы)
        echo CHtml::label('Источник данных:', 'ownerId');
	    echo CHtml::dropDownList('ownerId', 823, UserModule::getAdminList());
    ?>
    <br>
	<?php 
	    $this->widget('bootstrap.widgets.TbButton',
            array('buttonType'  => 'submit',
                  'type'        => 'primary',
                  'label'       => $model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save'),
                  'htmlOptions' => array('id' => 'save_questionary'),
        ));
    ?>
<?php $this->endWidget(); ?>
</div><!-- form -->