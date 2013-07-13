<?php
$this->breadcrumbs=array(
	'Catalog Sections'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List CatalogSection','url'=>array('index')),
	array('label'=>'Manage CatalogSection','url'=>array('admin')),
);
?>

<h1>Create CatalogSection</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>