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
/* @var $content string */
?><!DOCTYPE html>
<html lang="ru">
    <?php 
    // шапка страницы
    $this->renderPartial('//layouts/head_info');
    ?>
    <body class="smart-style-0 fixed-navigation">
        <?php 
        // верхняя панель с инструментами
        $this->renderPartial('//layouts/header');
        // главное меню
        $this->renderPartial('//layouts/aside_nav');
        // основное содержимое страницы
        $this->renderPartial('//layouts/main', array(
            'content' => $content,
        ));
        ?>
        <div id="shortcut"></div>
        <?php
        // полный набор скриптов для работы темы оформления
        $this->renderPartial('//layouts/scripts');
        // стандартный JS-код темы оформления, который должен находится на каждой странице для
        // корректной работы AJAX-навигации
        $this->renderPartial('//layouts/ajax/_pageSetup');
        ?>
    </body>
</html>