<?php
/**
 * Страница отбора актеров для заказчика
 * 
 * @var CustomerInvite $customerInvite
 */

// убираем из заголовка все лишнее
$this->ecHeaderOptions = array(
    'displayloginTool' => false,
    'displayInformer'  => false,
);

$this->breadcrumbs = array();

// блок всплывающих сообщений
$this->widget('bootstrap.widgets.TbAlert');
?>

<h1>Отбор актеров</h1>

<?php // список всех заявок
$this->widget('application.modules.projects.extensions.TokenSelection.TokenSelection', array(
    'objectType'     => $customerInvite->objecttype,
    'objectId'       => $customerInvite->objectid,
    'displayType'    => 'applications',
    'customerInvite' => $customerInvite,
));
?>