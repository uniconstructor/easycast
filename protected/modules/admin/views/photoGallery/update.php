<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
	'Галереи'=>array('/admin/PhotoGallery/admin'),
	'Редактировать',
);

$this->menu=array(
    array('label'=>'Список галерей','url'=>array('admin')),
    array('label'=>'Создать галерею','url'=>array('create')),
);
?>

<h1>Редактировать галерею <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>