<?php
$this->breadcrumbs = array(
    'Администрирование' =>array('/admin'),
	'Приглашения для заказчиков' => array('/admin/customerInvite/admin'),
	$model->name,
);

?>

<h1>Приглашение #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		//'objecttype',
		//'objectid',
		'key',
		'key2',
		'email',
		'name',
		'managerid',
		'timecreated',
		'timeused',
		'comment',
		'userid',
	),
)); ?>
