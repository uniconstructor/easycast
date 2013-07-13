<?php
/**
 * отображение страницы "мой выбор"
 * @var TbActiveForm $form
 */
$this->breadcrumbs=array(
    "Мой выбор",
);

?>

<h1>Мой выбор</h1>

<?php

// Выводим сообщения или предупреждения
$this->widget('bootstrap.widgets.TbAlert', array(
    'block' => true, // display a larger alert block?
    'fade'  => false, // use transitions?
    'closeText' => false, // close link text - if set to false, no close link is displayed
    'alerts' => array( // configurations per alert type
        'success' => array('block'=>true, 'fade'=>false), // success, info, warning, error or danger
        'info'    => array('block'=>true, 'fade'=>false), // success, info, warning, error or danger
    ),
));


// @todo разместить по центру дополнительные комментарии и кнопку оформления заказа
if ( ! FastOrder::orderIsEmpty() )
{// В заказе есть хотя бы один актер

    // Выводим всех приглашенных заказчиком актеров одним списком
    $this->widget('application.modules.catalog.extensions.MyChoice.MyChoice', array(
        'questionaries' => $questionaries,
    ));
    
    // добавляем форму с дополнительными комментариями к заказу и кнопкой "подтвердить"
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'order-form',
        'enableAjaxValidation' => false,
        'action' => '/site/placeOrder',
    ));?>
    <div class="span2">&nbsp;</div>
    <div class="span6">
        <h2>Ваши контактные данные</h2>
        <?php 
            echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>'));
            // ФИО
            echo $form->textFieldRow($order, 'name');
        ?>
        <?php 
            // телефон
            echo $form->textFieldRow($order, 'phone');
        ?>
        <?php 
            // email
            echo $form->labelEx($order,'email');
            echo $form->emailField($order, 'email');
            echo $form->error($order,'email');
        ?>
        <?php 
            // поле с дополнительной информацией о заказе
            echo $form->textAreaRow($order, 'comment', array('style' => 'width:100%;height:100px;'));
        ?>
        <div>
        <?php $this->widget('bootstrap.widgets.TbButton',
            array(
                    'buttonType'  => 'submit',
                    'type'        => 'success',
                    'label'       => 'Оформить заказ',
                    'htmlOptions' => array('id'=>'place_order', 'class' => 'btn btn-large btn-success',),
                )); 
        ?>
    	</div>
    </div>
    <div class="span2">&nbsp;</div>
    
    <? $this->endWidget(); 
}
?>