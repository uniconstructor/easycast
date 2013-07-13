<?php
$this->breadcrumbs=array(
	'Project Events',
);

$this->menu=array(
	array('label'=>'Create ProjectEvent','url'=>array('create')),
	array('label'=>'Manage ProjectEvent','url'=>array('admin')),
);
?>

<h1>Project Events</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
