<?php
/**
 * Отображение главного информера для гостей
 */
/* @var $this ECMainInformer */

// отображаем кнопку входа/выхода
$this->widget('ext.ECMarkup.ECLoginWidget.ECLoginWidget');
?>
<div class="ec-join_but">
    <a class="btn ec-btn-primary reg btn-lg" href="/user/registration"
        data-toggle="modal" data-target="#registration-modal">Регистрация</a>
</div>
