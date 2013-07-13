<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
	'Проекты'=>array('/admin/project/admin'),
	$model->name=>array('/admin/project/view','id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label'=>'Список проектов','url'=>array('/admin/project/admin')),
	array('label'=>'Создать проект','url'=>array('/admin/project/create')),
	array('label'=>'Просмотр проекта','url'=>array('/admin/project/view','id'=>$model->id)),
	//array('label'=>'Manage Project','url'=>array('admin')),
);
?>

<h1>Обновить проект "<?php echo $model->name; ?>"</h1>

<?php 
    echo $this->renderPartial('_form',
                            array(
                                'model'=>$model,

                                'video'=>$video,
                                'validatedVideos'=>$validatedVideos,
                            )
                        );
?>