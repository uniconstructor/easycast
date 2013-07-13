<?php
$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
    $model->event->project->name=>array('/admin/project/view', 'id' => $model->event->project->id),
    $model->event->name=>array('/admin/projectEvent/view','id'=>$model->event->id),
	$model->name,
);

$this->menu=array(
	array('label'=>'Страница мероприятия','url'=>array('/admin/projectEvent/view','id'=>$model->event->id)),
	array('label'=>'Добавить вакансию','url'=>array('/admin/eventVacancy/create', 'eventid'=>$model->event->id)),
	array('label'=>'Редактировать вакансию','url'=>array('/admin/eventVacancy/update','id'=>$model->id)),
	array('label'=>'Удалить вакансию','url'=>'#','linkOptions'=>
	            array('submit'=>array('/admin/eventVacancy/delete','id'=>$model->id),'confirm'=>'Вы уверены что хотите удалить эту вакансию?')),
    array('label'=>'Заявки на участие','url'=>array('/admin/projectMember/index', 'vacancyid'=>$model->id, 'type' => 'applications')),
	//array('label'=>'Manage EventVacancy','url'=>array('admin')),
);

// @todo подтверждение перед сменой статуса
if ( in_array('active', $model->getAllowedStatuses()) )
{
    $this->menu[] = array('label'=>'Открыть вакансию',
        'url'=>array('/admin/eventVacancy/setStatus', 'id'=>$model->id, 'status' => 'active'));
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{
    $this->menu[] = array('label'=>'Закрыть вакансию',
        'url'=>array('/admin/eventVacancy/setStatus', 'id'=>$model->id, 'status' => 'finished'));
}

$this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
));
?>

<h1>Вакансия "<?php echo $model->name; ?>"</h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'description:html',
		//'scopeid',
		'limit',
		array(
            'label' => ProjectsModule::t('status'),
            'value' => $model->statustext,
        ),
	),
)); ?>
