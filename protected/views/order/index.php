<?php
/**
 * Форма срочного заказа на отдельной странице
 */
/* @var $this OrderController */
 
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'     => 'order-form',
    'enableAjaxValidation' => false,
    'action' => '/site/placeOrder',
    'type'   => 'horizontal',
));
?>
<div class="row row-fluid">
<div class="span4">&nbsp;</div>
<div class="span6">
    <h2 style="text-align:center;">Заказ</h2>
    <div class="alert alert-block">
    Пожалуйста заполните форму, чтобы сделать заказ или задать нам вопрос.
    </div>
    <?php
    echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>'));
    // ФИО
    echo $form->textFieldRow($order, 'name');
    // телефон
    echo $form->textFieldRow($order, 'phone');
    // email
    echo $form->textFieldRow($order, 'email');
    // поле с дополнительной информацией о заказе
    echo $form->textAreaRow($order, 'comment', array('style' => 'width:100%;height:100px;'));
    ?>
    <div class="alert alert-info">
        <small>Мы обязуемся использовать ваш телефон и email только для связи с вами и уточнения деталей заказа.
    	Мы никогда не будем присылать вам рекламу или раскрывать ваши данные третьим лицам.</small>
	</div>
    <div style="text-align:center;">
        <?php 
        // кнопка оформления заказа
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'  => 'submit',
            'type'        => 'success',
            'size'        => 'large',
            'label'       => 'Отправить',
            'htmlOptions' => array(
                'id'    => 'place_order',
            ),
        )); 
        ?>
	</div>
</div>
<div class="span2">&nbsp;</div>
</div>
<?php 
// конец формы
$this->endWidget(); 
