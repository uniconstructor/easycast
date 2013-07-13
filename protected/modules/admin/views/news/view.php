<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
    'Новости' =>array('/admin/news/admin'),
	$model->name
);

$this->menu=array(
	array('label'=>'Список новостей','url'=>array('admin')),
	array('label'=>'Создать','url'=>array('create')),
	array('label'=>'Редактировать','url'=>array('update','id'=>$model->id)),
	array('label'=>'Удалить','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены что хотиите удалить эту новость?')),
);
?>

<h1>Просмотр новости <?php echo $model->name; ?></h1>

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
