<?php
/**
 * Страница отображаемая после завершения создания онлайн-кастинга
 */
/* @var $this OnlineCastingController */

$this->pageTitle = 'Онлайн-кастинг создан';

$this->breadcrumbs = array(
    'Онлайн-кастинг',
);

?>
<div class="page-alternate">
    <div class="container">
        <div class="title-page">
            <h1 class="title">Онлайн-кастинг создан</h1>
            <h4 class="intro-description">
                Спасибо что воспользовались нашим сервисом. Мы проверим указанные вами данные, оповестим
                подходящих участников и свяжемся с вами когда все будет готово.<br>
                Как только на кастинг начнут поступать первые заявки мы пришлем вам по электронной почте ссылку,
                по которой вы сможете отобрать участников.<br>
                Если у вас остались вопросы - воспользуйтесь онлайн-помощью внизу экрана, если наш оператор в сети - то 
                мы ответим вам в течении нескольких минут.<br>
            </h4>
        </div>
    </div>
    <div class="row text-center">
        <?php 
        // возврат на главную
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'link',
            'type'       => 'info',
            'size'       => 'large',
            'label'      => 'Вернуться на главную',
            'url'        => Yii::app()->createUrl('/site/index'),
        ));
        ?>
    </div>
</div>