<?php
/**
 * Редирект возвращающий обратно на страницу канала после заполнения формы
 */
/* @var $this TopModelController */
?>
<div class="page-alternate">
    <div class="container">
        <div class="row text-center">
            <div class="alert alert-success alert-block">
                Заявка успешно отправлена. Вы можете перейти обратно на сайт проекта.
            </div>
        </div>
        <div class="row text-center">
            <div class="title-page">
                <?php
                $image = CHtml::image('/images/tm_form_header.jpg');
                echo CHtml::link($image, 'http://u-tv.ru/castings/13/');
                ?>
            </div>
        </div>
        <div class="row text-center">
            <?php 
            echo CHtml::link('Вернуться на сайт проекта', 'http://u-tv.ru/castings/13/', array(
                'class' => 'btn btn-large btn-primary',
            ));
            ?>
        </div>
    </div>
</div>