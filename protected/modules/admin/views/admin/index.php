<?php
/**
 * Главная страница админки
 */
/* @var $this AdminController */

$this->breadcrumbs = array(
	'Администрирование',
);

// отображаение оповещений
$this->widget('bootstrap.widgets.TbAlert');
?>

<h1>Администрирование</h1>

<?php 
$this->menu = array(
    array('label' => 'Все проекты', 'url' => array('/admin/project/admin')),
    array('label' => 'Пользователи', 'url' => array('/user/admin')),
    array('label' => 'Анкеты', 'url' => array('/admin/questionary')),
    array('label' => 'Категории и группы','url' => array('/admin/category/index')),
    //array('label' => 'Галерея','url' => array('/admin/photoGallery/admin')),
    array('label' => 'Отправить коммерческое предложение', 'url' => array('/admin/customerOffer/create')),
    array('label' => 'Запустить рассылку (cron)', 'url' => array('/admin/admin/cron')),
    array('label' => 'Заказы', 'url' => array('/admin/fastOrder')),
    //array('label' => 'Новости','url' => array('/admin/news/admin')),
    //array('label' => '', 'url' => array('')),
);

?>
<div style="display:none;">
    <?php
    // пока не настроен автоматический запуск cron - он выполняется каждый раз при 
    // обращении к главной странице админки
    //Yii::app()->getModule('admin')->cron();
    ?>
</div>