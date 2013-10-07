<?php
/* @var $this AdminController */

$this->breadcrumbs=array(
	'Администрирование',
);

// отображаение оповещений
$this->widget('bootstrap.widgets.TbAlert', array(
    'block'     => true, // display a larger alert block?
    'fade'      => true, // use transitions?
    'closeText' => '&times;', // close link text - if set to false, no close link is displayed
    'alerts' => array( // configurations per alert type: success, info, warning, error or danger
        'success' => array('block' => true, 'fade' => true, 'closeText' => '&times;'),
    ),
));
?>

<h1>Администрирование</h1>

<?php 
$this->menu=array(
    array('label'=>'Проекты','url'=>array('/admin/project/admin')),
    array('label'=>'Заказы','url'=>array('/admin/fastOrder')),
    array('label'=>'Пользователи','url'=>array('/user/admin')),
    array('label'=>'Каталог и анкеты','url'=>array('/admin/questionary')),
    array('label'=>'Галерея','url'=>array('/admin/photoGallery/admin')),
    array('label'=>'Новости','url'=>array('/admin/news/admin')),
    array('label'=>'Статьи','url'=>array('/admin/article/admin')),
    //array('label'=>'','url'=>array('')),
);

?>
