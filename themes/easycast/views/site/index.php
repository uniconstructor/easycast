<?php
/**
 * Шаблон главной страницы: демонстрация каталога и меню заказчика и пользователя 
 */
/* @var $this SiteController */
if ( Yii::app()->getModule('user')->getViewMode() === 'customer' )
{// ассорти актеров на главной (для заказчиков)
    $this->widget('ext.ECMarkup.ECTopRated.ECTopRated');
    // Выводим меню пользователя и заказчика
    $this->widget('ext.ECMarkup.ECMainMenu.ECMainMenu');
    // список сервисов на главной (только для заказчиков)  
    $this->widget('ext.ECMarkup.EServiceList.EServiceList', array());
}else
{// @todo ассорти проектов (для участников)
    $this->widget('ext.ECMarkup.ECTopRated.ECTopRated');
    // Выводим меню пользователя и заказчика
    $this->widget('ext.ECMarkup.ECMainMenu.ECMainMenu');
}
