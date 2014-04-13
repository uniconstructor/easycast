<?php
/**
 * Страница входа на сайт
 */
/* @var $form TbActiveForm */

$this->pageTitle = UserModule::t("Login");
$this->breadcrumbs = array(
	UserModule::t("Login"),
);

$this->widget('bootstrap.widgets.TbAlert');
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span4"></div>
            <div class="span4 text-center">
                <h1><?= UserModule::t("Login"); ?></h1>
                <?php 
                $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                	'id'   => 'login-form',
                    'type' => 'horizontal'
                ));
                $passwordHint = CHtml::link(UserModule::t("Register"),Yii::app()->getModule('user')->registrationUrl);
                $passwordHint .= ' | '.CHtml::link(UserModule::t("Lost Password?"),Yii::app()->getModule('user')->recoveryUrl);
            	echo $form->errorSummary($model);
            	
            	echo $form->textFieldRow($model, 'username');
            	echo $form->passwordFieldRow($model, 'password');
            	echo $passwordHint;
            	echo $form->checkBoxRow($model, 'rememberMe');
            	
        	    $form->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'  => 'submit',
                    'type'        => 'primary',
                    'size'        => 'large',
                    'label'       => UserModule::t("Login"),
                ));
                ?>
                <?php $this->endWidget(); ?>
            </div>
            <div class="span4"></div>
        </div>
    </div>
</div>