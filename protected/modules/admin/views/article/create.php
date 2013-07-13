<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
	'Статьи' =>array('/admin/article/admin'),
	'Создать',
);

$this->menu=array(
	array('label'=>'Список статей','url'=>array('admin')),
);
?>

<h1>Создать статью</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>