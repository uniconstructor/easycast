<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
    'Список (с поиском)'=>array('/admin/PhotoGallery/admin'),
	'Список',
);

$this->menu=array(
	array('label'=>'Создать галерею','url'=>array('create')),
	array('label'=>'Список галерей','url'=>array('admin')),
);
?>

<h1>Список</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
