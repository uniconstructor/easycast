<?php
/**
 * Шаблон главной страницы: демонстрация каталога и меню заказчика и пользователя 
 */
/* @var $this SiteController */
if ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('Admin') OR Yii::app()->user->checkAccess('Customer') )
{// ассорти актеров на главной (для заказчиков)
    $this->widget('ext.ECMarkup.ECTopRated.ECTopRated');
    // короткая форма поиска на главной
    Yii::import('catalog.models.*');
    $rootSection = CatalogSection::model()->findByPk(1);
    /*$this->widget('catalog.extensions.search.QShortSearchForm.QShortSearchForm', array(
        'mode'          => 'form',
        'columnFilters' => array(
            'base'   => array('system'),
            'looks'  => array('age'),
            'skills' => array('gender'),
        ),
        'searchObject'        => $rootSection,
        'redirectUrl'         => '/catalog/catalog/search',
        'refreshDataOnChange' => false,
    ));*/
    // @todo вывести список сервисов на главной вместо краткого поиска    
    //$this->widget('ext.ECMarkup.EServiceList.EServiceList', array());
}else
{// @todo ассорти проектов (для участников)
    $this->widget('ext.ECMarkup.ECTopRated.ECTopRated');
}
// Выводим меню пользователя и заказчика
$this->widget('ext.ECMarkup.ECMainMenu.ECMainMenu');