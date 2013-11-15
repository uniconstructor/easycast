<?php
/**
 * Шаблон главной страницы: демонстрация каталога и меню заказчика и пользователя 
 */
/* @var $this SiteController */
if ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('Admin') OR Yii::app()->user->checkAccess('Customer') )
{// ассорти актеров на главной (для заказчиков)
    $this->widget('ext.ECMarkup.ECTopRated.ECTopRated');
}else
{// ассорти проектов (для участников)
    
}
// Выводим меню пользователя и заказчика
$this->widget('ext.ECMarkup.ECMainMenu.ECMainMenu');
