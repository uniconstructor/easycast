<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
    'Анкеты' =>array('/admin/questionary'),
    'Разделы каталога' => array('/admin/catalogSection/admin'),
	$model->name => array('/admin/catalogSection/view','id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'Список разделов','url'=>array('admin/catalogSection/admin')),
    //array('label'=>'List CatalogSection','url'=>array('index')),
	//array('label'=>'Create CatalogSection','url'=>array('create')),
	//array('label'=>'View CatalogSection','url'=>array('view','id'=>$model->id)),
	
);
?>

<h1>Редактировать раздел <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>