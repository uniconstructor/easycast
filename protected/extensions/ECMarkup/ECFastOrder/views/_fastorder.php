<?php

/**
 * Форма для срочного заказа в главном меню
 */

// отображаем всплывающее окно
$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>$data->modalid));

// отображаем форму
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>$data->formid,
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
    'clientOptions' => array(
        'validateOnSubmit'=>true,
        'validateOnChange'=>false,
    ),
    'action' => $data->action,
));

?>

<div class="modal-header">
    <a class="close white" data-dismiss="modal">&times;</a>
    <h3><?php echo $data->header; ?></h3>
</div>

<div class="modal-body">
	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>128)); ?>
	<?php echo $form->textFieldRow($model,'phone',array('class'=>'span5','maxlength'=>20)); ?>
	<?php echo $form->textFieldRow($model,'email',array('class'=>'span5','maxlength'=>255)); ?>
	<?php echo $form->textAreaRow($model,'comment',array('class'=>'span5','maxlength'=>255)); ?>
</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'ajaxSubmit',
			'type'=>'primary',
			'label'=>  Yii::t('coreMessages', 'place_order'),
            'htmlOptions' => array(
                'id'=>$data->submitid
            ),
            'url'     => $data->action,
            'ajaxOptions' => array(
                'dataType'=>'json',
                'success' => $data->ajaxSuccessScript,
                'type'    => 'post',
                'url'     => $data->action,
                'data'    => new CJavaScriptExpression('$("#'.$data->formid.'").serialize()'),
            ),
		)); ?>
    
    <?php 
    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => Yii::t('coreMessages', 'cancel'),
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); 
    ?>
</div>

<?php $this->endWidget($data->formid); //form ?>
<?php $this->endWidget($data->modalid); // modal ?>
