<?php
$this->breadcrumbs=array(
	'Catalog Sections',
);

$this->menu=array(
	array('label'=>'Create CatalogSection','url'=>array('create')),
	array('label'=>'Manage CatalogSection','url'=>array('admin')),
);
?>

<h1>Catalog Sections</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
