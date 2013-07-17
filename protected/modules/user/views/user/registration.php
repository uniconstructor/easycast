<?php 
/**
 * Страница регистрации пользователя
 * 
 * @todo языковые строки
 */

$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Registration");
$this->breadcrumbs=array(
	UserModule::t("Registration"),
);
?>

<?php if(Yii::app()->user->hasFlash('registration')): ?>
<div class="success">
<?php echo Yii::app()->user->getFlash('registration'); ?>
</div>
<?php else: ?>

<div class="form span6">
    <h1><?php echo UserModule::t("Registration"); ?></h1>
    <div class="alert alert-info">
        Для регистрации достаточно ввести только почту и проверочный код.
    </div>
    <?php $form = $this->beginWidget('UActiveForm', array(
    	'id'=>'registration-form',
    	'enableAjaxValidation'=>true,
    	'disableAjaxValidationAttributes'=>array('RegistrationForm_verifyCode'),
    	'clientOptions'=>array(
    		'validateOnSubmit'=>true,
    	),
    	'htmlOptions' => array('enctype'=>'multipart/form-data'),
    )); ?>

	<?php echo $form->errorSummary(array($model, $profile)); ?>
	<?php echo $form->textFieldRow($model,'email'); ?>
	
	<?php if (UserModule::doCaptcha('registration')): ?>
		<br>
		<?php $this->widget('CCaptcha'); ?>
		<?php echo $form->textFieldRow($model,'verifyCode'); ?>
		<div class="alert">
    		<small>
    		    <?php echo UserModule::t("Please enter the letters as they are shown in the image above."); ?>
    		    <br/>
    		    <?php echo UserModule::t("Letters are not case-sensitive."); ?>
    	    </small>
	    </div>
	<?php endif; ?>
	
	<!-- Сворачивающийся блок с дополнительными полями -->
    <div class="accordion" id="accordion2">
        <?php $this->beginWidget('bootstrap.widgets.TbCollapse', array(
            'toggle' => false,
        ));
        ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                Дополнительно...
                </a>
            </div>
            <div id="collapseOne" class="accordion-body collapse">
                <div class="accordion-inner">
                    <?php echo $form->textFieldRow($model,'username'); ?>
                	<div class="alert" style="margin-bottom:5px;">
                    	<small>
                    	    Минимальная длина логина - 2 символа.<br>
                    	    Если логин не задан - то он будет создан из вашего почтового адреса.<br>
                    	    Вы также можете использовать адрес электронной почты вместо логина для входа на сайт.
                        </small>
                	</div>
                	<?php echo $form->passwordFieldRow($model,'password', array('value' => '')); ?>
                	<?php echo $form->passwordFieldRow($model,'verifyPassword', array('value' => '')); ?>
                	<div class="alert">
                    	<small>
                    	    Минимальная длина пароля - 6 символов.<br>
                    	    Если пароль не задан - он будет создан автоматическии отправлен вам на почту.
                        </small>
                	</div>
                </div>
            </div>
        </div>
        <?php $this->endWidget();?>
    </div>
    <!-- Конец скрытого блока -->
    
    <?php // кнопка регистрации 
	    $this->widget('bootstrap.widgets.TbButton',
            array('buttonType'=>'submit',
                  'type'  => 'primary',
                  'label' => UserModule::t("Register"),
                  'htmlOptions' => array('class' => 'btn btn-large btn-primary'),
        ));
    ?>
    
<?php
    // конец формы 
    $this->endWidget();
?>


</div><!-- form -->
<?php endif; ?>