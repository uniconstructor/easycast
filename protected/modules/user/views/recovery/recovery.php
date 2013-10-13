<?php 
/**
 * Страница восстановления пароля
 * @todo языковые строки
 */

$this->pageTitle = Yii::app()->name . ' - '."Восстановление пароля";

$this->breadcrumbs=array(
	UserModule::t("Login") => array('/user/login'),
	UserModule::t("Restore"),
);
?>

<h1>Восстановление пароля</h1>

<?php if(Yii::app()->user->hasFlash('recoveryMessage')): ?>
<div class="alert alert-success">
<?php echo Yii::app()->user->getFlash('recoveryMessage'); ?>
</div>
<?php else: ?>
<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo CHtml::errorSummary($form); ?>
	
	<?php echo CHtml::activeLabel($form,'login_or_email'); ?>
	<?php echo CHtml::activeTextField($form,'login_or_email') ?>
	<p class="hint"><?php echo UserModule::t("Please enter your login or email addres."); ?></p>
	
	<?php echo CHtml::submitButton(UserModule::t("Restore"), array('class' => 'btn btn-primary')); ?>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->
<?php endif; ?>