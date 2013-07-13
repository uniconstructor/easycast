<?php
$this->breadcrumbs=array(
	'News',
);

$this->menu=array(
	array('label'=>'Create News','url'=>array('create')),
	array('label'=>'Manage News','url'=>array('admin')),
);
?>

<h1>News</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
