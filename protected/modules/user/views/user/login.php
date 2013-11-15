<?php
/**
 * Страница входа на сайт
 */

$this->pageTitle = UserModule::t("Login");
$this->breadcrumbs = array(
	UserModule::t("Login"),
);
?>

<?php if(Yii::app()->user->hasFlash('loginMessage')): ?>
<div class="success">
	<?php echo Yii::app()->user->getFlash('loginMessage'); ?>
</div>
<?php endif; ?>

<div class="form span6 offset4">
    <h1><?php echo UserModule::t("Login"); ?></h1>
    <?php 
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        	'id' => 'login-form',
        ));
    ?>
	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->textFieldRow($model,'username') ?>
	<?php echo $form->passwordFieldRow($model,'password') ?>
	<p class="hint">
    	<?php echo CHtml::link(UserModule::t("Register"),Yii::app()->getModule('user')->registrationUrl); ?> | 
    	<?php echo CHtml::link(UserModule::t("Lost Password?"),Yii::app()->getModule('user')->recoveryUrl); ?>
	</p>
	<?php echo $form->checkBoxRow($model,'rememberMe'); ?>
    <br>
	<?php $this->widget('bootstrap.widgets.TbButton',
        array(
            'buttonType'  => 'submit',
            'type'        => 'primary',
            'label'       => UserModule::t("Login"),
            'htmlOptions' => array('class' => 'btn btn-large btn-primary'),
        )); 
    ?>
<?php $this->endWidget(); ?>
</div><!-- form -->