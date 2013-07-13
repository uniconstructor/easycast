<?php
/* @var $this AdminController */

$this->breadcrumbs=array(
	'Администрирование',
);
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
