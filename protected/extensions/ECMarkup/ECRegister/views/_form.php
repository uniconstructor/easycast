<?php
/**
 * Форма регистрации участника (во всплывающем окне)
 */

// отображаем всплывающее окно
$this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'registration-modal'));

// отображаем форму
$form = $this->beginWidget('UActiveForm', array(
    'id' => 'registration-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        //'afterValidate' => new CJavaScriptExpression("function(form, data, hasError){console.log(hasError);if(!hasError){window.location.href='{$this->actionUrl}';} }"),
        'afterValidate' => new CJavaScriptExpression("function(form, data, hasError){console.log(hasError);if(!hasError){window.location.href='{$this->redirectUrl}';return false;}}"),
    ),
    'htmlOptions' => array('enctype'=>'multipart/form-data'),
    'action' => $this->actionUrl,
));
?>

<div class="modal-header">
    <a class="close white" data-dismiss="modal">&times;</a>
    <h3><?php echo UserModule::t("Registration"); ?></h3>
</div>

<div class="modal-body">
	<?php echo $form->errorSummary(array($model)); ?>
	<?php echo $form->textFieldRow($model, 'email'); ?>
	
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
</div>

<div class="modal-footer">
    <?php 
    // кнопка регистрации 
    $this->widget('bootstrap.widgets.TbButton', array(
          'buttonType'  => 'submit',
          'type'        => 'success',
          'label'       => UserModule::t("Register"),
          //'htmlOptions' => array('class' => 'btn btn-success'),
          'url'     => $this->actionUrl,
          /*'ajaxOptions' => array(
            'beforeSend' => new CJavaScriptExpression("function(){\$('#registration-form').yiiactiveform().validate(); return false;}"),
            //'beforeSend' => new CJavaScriptExpression("function(){console.log($('#registration-form')); return false;}"),
            //'success'  => 'function () {window.location.href = "'.$this->redirectUrl.'";}',
            'type'     => 'post',
            'error'    => 'function() {return false;}',
            //'url'     => $this->actionUrl,
            //'data'    => new CJavaScriptExpression('$("#registration-form").serialize()'),
          ),*/
    ));
    // закрыть окно
    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => Yii::t('coreMessages', 'cancel'),
        'htmlOptions' => array('data-dismiss' => 'modal', 'class' => 'pull-left'),
    )); 
    ?>
</div>
<?php
    // конец формы 
    $this->endWidget('registration-form');
    // конец окна
    $this->endWidget('registration-modal');
?>

