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
    array('label' => 'Проекты', 'url' => array('/admin/project/admin')),
    array('label' => 'Заказы', 'url' => array('/admin/fastOrder')),
    array('label' => 'Пользователи', 'url' => array('/user/admin')),
    array('label' => 'Каталог и анкеты', 'url' => array('/admin/questionary')),
    array('label' => 'Галерея','url' => array('/admin/photoGallery/admin')),
    array('label' => 'Отправка коммерческих приглашений', 'url' => array('/admin/customerOffer/create')),
    array('label' => 'Выполнить действия по расписанию (cron)', 'url' => array('/admin/admin/cron')),
    //array('label' => 'Новости','url' => array('/admin/news/admin')),
    //array('label' => '', 'url' => array('')),
);

?>
<div style="display:none;">
    <?php
    // пока не настроен автоматический запуск cron - он выполняется каждый раз при 
    // обращении к главной странице админки
    Yii::app()->getModule('admin')->cron();
    ?>
</div>