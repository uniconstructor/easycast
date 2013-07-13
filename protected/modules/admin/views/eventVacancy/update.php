<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
    $model->event->project->name=>array('/admin/project/view', 'id' => $model->event->project->id),
    $model->event->name=>array('/admin/projectEvent/view','id'=>$model->event->id),
	$model->name=>array('view','id'=>$model->id),
	'Редактирование',
);

$this->menu=array(
	array('label'=>'Страница мероприятия','url'=>array('/admin/projectEvent/view','id'=>$model->event->id)),
	array('label'=>'Добавить вакансию','url'=>array('/admin/eventVacancy/create', 'eventid'=>$model->event->id)),
	array('label'=>'Просмотреть вакансию','url'=>array('/admin/eventVacancy/view','id'=>$model->id)),
	//array('label'=>'Manage EventVacancy','url'=>array('admin')),
);
?>

<h1>Редактирование вакансии "<?php echo $model->name; ?>"</h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>