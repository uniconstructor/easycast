<?php
/**
 * Разметка страницы "мой выбор"
 * @todo подключить проверку телефонного момера через LPNValidator
 */
/* @var $form  TbActiveForm */
/* @var $order FastOrder */
/* @var $this  CatalogController */

$this->breadcrumbs = array(
    "Мой выбор",
);

?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Мой выбор</h1>
                    <h3 class="title-description">
                        На этой странице вы можете еще раз просмотреть всю информацию о 
                        выбранных вами участниках и оформить заказ.
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            // Выводим сообщения или предупреждения
            $this->widget('bootstrap.widgets.TbAlert');
            if ( ! FastOrder::orderIsEmpty() )
            {// В заказе есть хотя бы один актер
                // Выводим всех приглашенных заказчиком актеров одним списком
                $this->widget('application.modules.catalog.extensions.MyChoice.MyChoice', array(
                    'questionaries' => $questionaries,
                ));
                
                // добавляем форму с дополнительными комментариями к заказу и кнопкой "подтвердить"
                $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                    'id'                   => 'order-form',
                    'enableAjaxValidation' => false,
                    'action'               => '/site/placeOrder',
                ));
                ?>
                <div class="title-page">
                    <h4 class="title">Ваши контактные данные</h4>
                    <h4 class="title-description">
                        Пожалуйста заполните форму, для того чтобы мы могли связаться с вами и подтвердить заказ.<br> 
                        Как только ваш заказ будет подтвержден мы свяжемся с каждым участником и сообщим ему всю необходимую информацию.
                    </h4>
                </div>
                <div class="span3">&nbsp;</div>
                <div class="span6">
                    <?php 
                    echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>'));
                    // ФИО
                    echo $form->textFieldRow($order, 'name',
                        array(
                            'placeholder' => 'Имя'
                        ),
                        array(
                            'prepend'     => '<i class="icon icon-user"></i>',
                        )
                    );
                    // телефон
                    echo $form->textFieldRow($order, 'phone', 
                        array(
                            'placeholder' => '(987)654-32-10'
                        ), 
                        array(
                            'hint'        => '<small>В номере должно быть 10 цифр. Мы обязуемся использовать ваш 
                                телефон и email только для связи с вами и уточнения деталей заказа.
                	            Мы никогда не будем присылать вам рекламу или раскрывать ваши данные 
                                третьим лицам.</small>',
                            'prepend'     => '+7',
                        )
                    );
                    // email
                    echo $form->textFieldRow($order, 'email', array(
                            'size'        => 60,
                            'maxlength'   => 255,
                            'placeholder' => 'mail@example.com',
                        ),
                        array(
                            'prepend'     => '<i class="icon icon-envelope"></i>',
                        )
                    );
                    // поле с дополнительной информацией о заказе
                    echo $form->textAreaRow($order, 'comment', array('style' => 'width:100%;height:100px;'));
                    ?>
                    <div>
                    <?php 
                    // и главная кнопка :)
                    $form->widget('bootstrap.widgets.TbButton', array(
                        'buttonType'  => 'submit',
                        'type'        => 'success',
                        'label'       => 'Оформить заказ',
                        'htmlOptions' => array(
                            'id'    => 'place_order',
                            'class' => 'btn btn-large btn-success',
                        ),
                    )); 
                    ?>
                	</div>
                </div>
                <div class="span3">&nbsp;</div>
                <?php 
                // конец формы
                $this->endWidget(); 
            }// конец условия "в заказе есть хотя бы один актер"
            ?>
        </div>
    </div>
</div>