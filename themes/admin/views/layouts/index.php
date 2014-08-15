<?php
/**
 * Главный файл разметки темы оформления "SmartAdmin". Тема построена на базе TwitterBootstrap.
 * Содержит все необходимые скрипты и стили.
 * Не подключайте никаких стандартных скриптов и стилей Yii, jQuery или Bootstrap при ее использовании.
 * Помните, что:
 * - вся основная конфигурация лежит в /js/app.js или /js/app.config.js
 * - виджеты JARVIS по умолчанию отключены в мобильной версии (см. app.js)
 * - все скрипты подключаются только внизу страницы, не перемещайте их
 * 
 * @see документация по этой теме оформления и всем ее виджетам лежит в ветке dev проекта easycast
 * @see http://192.241.236.31/themes/preview/smartadmin/1.4.1/ajaxversion/#ajax/dashboard.html
 * @see http://wrapbootstrap.com/preview/WB0573SK0
 * 
 * @todo 
 */
/* @var $this Controller */
/* @var $content string - основное содержимое страницы */
?><!DOCTYPE html>
<html lang="ru">
    <?php $this->renderPartial('//layouts/head_info'); ?>
    <body class="smart-style-0 fixed-navigation">
        <?php $this->renderPartial('//layouts/header'); ?>
        <?php $this->renderPartial('//layouts/aside_nav'); ?>
        <?php 
        // основное содержимое страницы
        $this->renderPartial('//layouts/main', array(
            'content' => $content,
        ));
        ?>
        <div id="shortcut"></div>
        <?php $this->renderPartial('//layouts/scripts'); ?>
        <?php $this->renderPartial('//layouts/ajax/_pageSetup'); ?>
    </body>
</html>