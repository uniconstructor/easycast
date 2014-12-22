<?php 
/**
 * Разметка одной страницы с заголовком и статистикой
 */
/* @var $this    SmartAdminController */
/* @var $content string */

if ( $this->pageHeader OR $this->sparks )
{// выводим блок с заголовком/статистикой страницы если есть данные для него
    $this->render('//layouts/title');
}

echo $content;

// js, обязательный для работы всех страниц темы SmartAdmin:
// отвечает за инициализацию всех элементов и за подгрузку содержимого страницы по AJAX
$this->renderPartial('//layouts/ajax/_pageSetup');
