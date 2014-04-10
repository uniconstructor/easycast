<?php
/**
 * Шаблон главной страницы: демонстрация каталога и меню заказчика и пользователя 
 */
/* @var $this SiteController */

// ассорти актеров на главной (для заказчиков) или ассорти проектов (для участников)
//$this->widget('ext.ECMarkup.ECTopRated.ECTopRated');
// Выводим меню пользователя и заказчика

$this->widget('ext.ResponsiveMenu.ResponsiveMenu', array(
    'items' => array(
        // меню заказчика
        array(
            'text' => 'Срочный заказ',
            'url'  => '#',
            'icon' => '<i aria-hidden="true" class="icon icon-shopping-cart"></i>',
            'linkOptions' => array(
                'data-toggle' => 'modal',
                'data-target' => '#fastOrderModal',
            ),
        ),
        array(
            'text' => 'Наши лица',
            'url'  => '/catalog/catalog/faces',
            'icon' => '<i aria-hidden="true" class="icon icon-group"></i>',
        ),
        array(
            'text' => 'Поиск',
            'url'  => '/search',
            'icon' => '<i aria-hidden="true" class="icon icon-search"></i>',
        ),
        array(
            'text' => 'Наши проекты',
            'url'  => '/projects',
            'icon' => '<i aria-hidden="true" class="icon icon-film"></i>',
        ),
        array(
            'text' => 'Онлайн-кастинг',
            'url'  => '/onlineCasting',
            'icon' => '<i aria-hidden="true" class="icon icon-laptop"></i>',
        ),
        // меню участника
    ),
));

//$this->widget('ext.ECMarkup.EServiceList.EServiceList');

/*if ( Yii::app()->getModule('user')->getViewMode() === 'customer' )
{
    // список сервисов на главной (только для заказчиков)  
    $this->widget('ext.ECMarkup.EServiceList.EServiceList');
}else
{
    // слайдер с новыми событиями для участников
    $this->widget('ext.ECMarkup.ECNewEvents.ECNewEvents');
}
*/