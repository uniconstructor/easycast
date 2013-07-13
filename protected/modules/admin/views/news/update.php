<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
    'Новости' =>array('/admin/news/admin'),
	$model->name=>array('view','id'=>$model->id),
	'Обновить',
);

$this->menu=array(
	array('label'=>'Список новостей','url'=>array('admin')),
	array('label'=>'Создать','url'=>array('create')),
	array('label'=>'Просмотр','url'=>array('view','id'=>$model->id)),
);
?>

<h1>Редактирование новости <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>