<?php

$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Отложенные анкеты',
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

<h1>Отложенные анкеты</h1>

<?php 

$this->widget('admin.extensions.QDelayed.QDelayed');

?>