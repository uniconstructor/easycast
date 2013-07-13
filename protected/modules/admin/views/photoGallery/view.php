<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
	'Галереи'=>array('/admin/PhotoGallery/admin'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List PhotoGallery','url'=>array('index')),
	array('label'=>'Create PhotoGallery','url'=>array('create')),
	array('label'=>'Update PhotoGallery','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete PhotoGallery','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage PhotoGallery','url'=>array('admin')),
);
?>

<h1>View PhotoGallery #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		//'id',
		'name',
		//'description',
		//'timecreated',
		//'timemodified',
		//'galleryid',
		'visible',
	),
)); ?>
