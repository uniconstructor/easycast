<?php 

$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Change Password");

$this->breadcrumbs=array(
	UserModule::t("Login") => array('/user/login'),
	UserModule::t("Change Password"),
);
?>

<h1><?php echo UserModule::t("Change Password"); ?></h1>


<div class="form">
<?php echo CHtml::beginForm(); ?>

	<p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>
	<?php echo CHtml::errorSummary($form); ?>
	
	<?php echo CHtml::activeLabelEx($form,'password'); ?>
	<?php echo CHtml::activePasswordField($form,'password', array('autocomplete' => 'off')); ?>
	<p class="hint">
	<?php echo UserModule::t("Minimal password length 4 symbols."); ?>
	</p>
	
	<?php echo CHtml::activeLabelEx($form,'verifyPassword'); ?>
	<?php echo CHtml::activePasswordField($form,'verifyPassword', array('autocomplete' => 'off')); ?>
	
	<div class="submit">
	<?php echo CHtml::submitButton(UserModule::t("Save"), array('class' => 'btn btn-primary')); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->