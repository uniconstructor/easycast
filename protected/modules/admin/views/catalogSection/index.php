<?php
/**
 * @todo эта страница не используется - удалить при рефакторинге
 */

$this->breadcrumbs=array(
	'Catalog Sections',
);

$this->menu=array(
	array('label'=>'Create CatalogSection','url'=>array('create')),
	array('label'=>'Manage CatalogSection','url'=>array('admin')),
);
?>

<h1>Разделы каталога</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider' => $dataProvider,
	'itemView'     => '_view',
)); ?>
