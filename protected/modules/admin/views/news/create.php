<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
	'Новости' =>array('/admin/news/admin'),
	'Создать',
);

$this->menu=array(
	array('label'=>'Список новостей','url'=>array('admin')),
);
?>

<h1>Создать</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>