<?php
/**
 * Создание нового приглашения на отбор актеров
 */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
	'Приглашения для заказчиков' => array('/admin/customerInvite/admin'),
	'Новое приглашение',
);

$this->menu = array(
	//array('label'=>'List CustomerInvite','url'=>array('index')),
	//array('label'=>'Manage CustomerInvite','url'=>array('admin')),
);
?>

<h1>Новое приглашение на отбор актеров</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>