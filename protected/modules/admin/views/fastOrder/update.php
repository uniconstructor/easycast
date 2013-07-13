<?php
$this->breadcrumbs=array(
	'Срочные заказы'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Пометить выполненным',
);

$this->menu=array(
	array('label'=>'Список заказов','url'=>array('index')),
	// array('label'=>'View FastOrder','url'=>array('view','id'=>$model->id)),
	//array('label'=>'Manage FastOrder','url'=>array('admin')),
);
?>

<h1>Пометить выполненным <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>