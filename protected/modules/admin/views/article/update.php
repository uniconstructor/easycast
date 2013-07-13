<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
	'Статьи' =>array('/admin/article/admin'),
	$model->name=>array('view','id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'Список статей','url'=>array('admin')),
	array('label'=>'Создать','url'=>array('create')),
	array('label'=>'Просмотр','url'=>array('view','id'=>$model->id)),
	
);
?>

<h1>Редактировать статью <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>