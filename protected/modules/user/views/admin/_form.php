<?php 
/**
 * Форма создания/редактирования пользователя
 * @var User $model
 */

?>
<div class="form">
<?php 
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'                   => 'user-form',
	'enableAjaxValidation' => true,
	'htmlOptions'          => array('enctype' => 'multipart/form-data'),
));
?>
	<!--p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p-->
	<?php echo $form->errorSummary(array($model)); ?>
	<?php echo $form->textFieldRow($model, 'email', array('autocomplete' => 'off', 'maxlength' => 255)); ?>
	<?php echo $form->dropDownListRow($model, 'superuser', User::itemAlias('AdminStatus')); ?>
	<?php echo $form->dropDownListRow($model, 'status', User::itemAlias('UserStatus')); ?>
	<?php // источник данных (или "владелец анкеты"). Кого указать в качестве "автора" анкеты?
        // 823 - это номер пользователя Светланы (стоит здесь по умолчанию на время ввода ее базы)
        // используется если мы вводим анкеты не из своей базы, а из чужой (по договоренности)
        if ( $model->isNewRecord )
        {// источник данных указывается только при создании
            echo CHtml::label('Источник данных:', 'ownerId');
            
            $moduleClass = get_class(Yii::app()->getModule('user'));
            $admins  = $moduleClass::getAdminList();
            $sources = array('0' => 'easyCast (наша анкета)');
            if ( isset($admins[823]) )
            {// из источников данных оставляем только Светлану (hardcoded value, но что делать)
                $sources[823] = $admins[823];
            }
            echo CHtml::dropDownList('ownerId', User::getDefaultOwnerId(), $sources);
        }
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