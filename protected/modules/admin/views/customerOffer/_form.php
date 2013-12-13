<?php 
/**
 * Форма создания/редактирования коммерческого предложения
 * @todo сделать 3 кнопки "создать", "сохранить" и "отправить"
 * @todo добавить возможность вставлять комментарий в коммерческое
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'customer-offer-form',
	'enableAjaxValidation' => true,
));


?>
	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->textFieldRow($model, 'email', array('class'=>'span5', 'maxlength' => 255)); ?>
	<?php echo $form->textFieldRow($model, 'name',  array('class'=>'span5', 'maxlength' => 255)); ?>
	
	<?php // Наш комментарий для заказчика
	/*echo $form->labelEx($model, 'comment'); 
    $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'comment',
    	'options' => array(
    		'lang' => 'ru',
            ),
    ));
    echo $form->error($model, 'comment');*/
    ?>
    <!--div class="alert alert-info">
        Этот комментарий добавится в письмо с приглашением, в самый конец. Используйте это поле
        чтобы сообщить заказчику дополнительную информацию.
    </div-->

	<div class="form-actions">
		<?php
		if ( $model->isNewRecord )
		{
		    $this->widget('bootstrap.widgets.TbButton', array(
		        'buttonType' => 'submit',
		        'type'  => 'primary',
		        'label' => 'Отправить',
		    ));
		} 
        ?>
	</div>

<?php $this->endWidget(); ?>
