<?php 
/**
 * Минимальная разметка для страницы админки c произвольной разметкой
 * (или пустой страницы/pfukeirb)
 */
/* @var $this    Controller */
/* @var $content string */

echo $content;

// js, обязательный для работы всех страниц темы SmartAdmin:
// отвечает за инициализацию всех элементов и за подгрузку содержимого страницы по AJAX
$this->renderPartial('//layouts/ajax/_pageSetup');