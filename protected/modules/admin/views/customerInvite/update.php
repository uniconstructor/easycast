<?php
$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
	'Приглашения для заказчиков' => array('/admin/customerInvite/admin'),
	'Приглашение №'.$model->id => array('/admin/customerInvite/view', 'id'=>$model->id),
	'Редактирование',
);

$this->menu=array(
	array('label' => 'Список приглашений','url'=>array('/admin/customerInvite/admin')),
	//array('label' => 'Новое приглашение','url'=>array('create')),
	array('label' => 'Просмотр','url'=>array('/admin/customerInvite/view','id' => $model->id)),
);
?>

<h1>Редактировать приглашение №<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>