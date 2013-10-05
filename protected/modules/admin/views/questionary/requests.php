<?php
/**
 * Список анкет на проверку
 */

$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Анкеты на проверку',
);

// Сообщение о том что анкета одобрена или отклонена
$this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true,
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( 
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        'warning'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
    ),
));
?>

<h1>Анкеты на проверку</h1>

<?php 

$this->widget('admin.extensions.QRequests.QRequests');

?>