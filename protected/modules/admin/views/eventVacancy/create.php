<?php

$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
    $event->project->name=>array('/admin/project/view', 'id' => $event->project->id),
    $event->name=>array('/admin/projectEvent/view','id'=>$event->id),
	'Добавить вакансию',
);

$this->menu=array(
	array('label'=>'Страница мероприятия','url'=>array('/admin/projectEvent/view','id'=>$event->id)),
	//array('label'=>'Manage EventVacancy','url'=>array('admin')),
);
?>

<h1>Добавить вакансию</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'event' => $event)); ?>