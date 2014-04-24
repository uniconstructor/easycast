<?php 
/**
 * Страница регистрации пользователя
 * 
 * @todo языковые строки
 */

$this->pageTitle = Yii::app()->name . ' - '.UserModule::t("Registration");
/*$this->breadcrumbs = array(
	UserModule::t("Registration"),
);*/

// @deprecated убираем из заголовка все лишнее
/*$this->ecHeaderOptions = array(
    'displayloginTool' => true,
    'displayInformer'  => false,
);*/

$this->widget('bootstrap.widgets.TbAlert');
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span3"></div>
            <div class="form span6 text-center">
                <h1 style="text-align:center;"><?php echo UserModule::t("Registration"); ?></h1>
                <div class="alert alert-info">
                    Для регистрации достаточно ввести только почту и проверочный код.
                </div>
                <?php 
                $form = $this->beginWidget('UActiveForm', array(
                	'id' => 'registration-form',
                	'enableAjaxValidation' => true,
                	//'disableAjaxValidationAttributes'=>array('RegistrationForm_verifyCode'),
                	'clientOptions' => array(
                		'validateOnSubmit' => true,
                        'validationDelay'  => 1200,
                        'validateOnChange' => true,
                	),
                	'htmlOptions' => array('enctype' => 'multipart/form-data'),
                )); 
                ?>
            
            	<?php echo $form->errorSummary(array($model, $profile)); ?>
            	<?php echo $form->textFieldRow($model,'email'); ?>
            	
            	<?php if (UserModule::doCaptcha('registration')): ?>
            		<br>
            		<?php $this->widget('CCaptcha', array(
            		    'captchaAction' => '//site/captcha',
                            'buttonOptions' => array(
                                'style' => 'width:100px;height:45px;',
                                'class' => 'btn btn-small'
                                )
            		        )
            		    ); 
                    ?>
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
                    <?php 
                    $this->beginWidget('bootstrap.widgets.TbCollapse', array(
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
                    <?php
                    // конец виджета "accordion" (сворачивающегося блока) 
                    $this->endWidget();
                    ?>
                </div>
                <!-- Конец свернутого блока -->
                <div style="display:block;text-align:center;">
                    <?php 
                    // кнопка регистрации 
            	    $this->widget('bootstrap.widgets.TbButton', array(
                        'buttonType'  => 'submit',
                        'type'        => 'primary',
                        'label'       => UserModule::t("Register"),
                        'htmlOptions' => array('class' => 'btn btn-large btn-success'),
                    ));
                    ?>
                </div>
                <?php
                // конец формы 
                $this->endWidget();
                ?>
            </div><!-- form -->
            <div class="span3"></div>
        </div>
    </div>
</div>
