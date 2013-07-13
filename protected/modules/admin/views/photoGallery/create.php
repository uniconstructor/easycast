<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
	'Галереи'=>array('/admin/PhotoGallery/admin'),
	'Создать',
);

$this->menu=array(
	array('label'=>'Список галерей','url'=>array('admin')),
);
?>

<h1>Создать галерею</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>