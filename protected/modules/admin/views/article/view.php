<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
	'Статьи' =>array('/admin/article/admin'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Список статей','url'=>array('admin')),
	array('label'=>'Создать статью','url'=>array('create')),
	array('label'=>'Редактировать статью','url'=>array('update','id'=>$model->id)),
	array('label'=>'Удалить статью','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	
);
?>

<h1>Статья <?php echo $model->name; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'description:html',
		'content:html',
        array(
            'label' => 'Создано',
            'value' => date('Y-m-d H:i', $model->timecreated),
        ),
		array(
            'label' => 'Последнее изменение',
            'value' => date('Y-m-d H:i', $model->timemodified),
        ),
		//'authorid',
		'visible',
	),
)); ?>
