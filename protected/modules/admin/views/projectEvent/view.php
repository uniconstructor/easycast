<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
    'Проект "'.$model->project->name.'"'=>array('/admin/project/view', 'id' => $model->project->id),
	$model->name,
);

$this->menu=array(
	array('label'=>'Страница проекта','url'=>array('/admin/project/view', 'id' => $model->project->id)),
	array('label'=>'Создать мероприятие','url'=>array('/admin/projectEvent/create', 'projectid'=>$model->project->id)),
	array('label'=>'Редактировать мероприятие','url'=>array('update','id'=>$model->id)),
	array('label'=>'Удалить мероприятие','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены, что хотите удалить это мероприятие?')),
    array('label'=>'Добавить вакансию','url'=>array('/admin/eventVacancy/create', 'eventid'=>$model->id)),
    array('label'=>'Заявки на участие','url'=>array('/admin/projectMember/index', 'eventid'=>$model->id, 'type' => 'applications')),
	//array('label'=>'Manage ProjectEvent','url'=>array('admin')),
);

// @todo подтверждение перед сменой статуса
if ( in_array('active', $model->getAllowedStatuses()) )
{
    $this->menu[] = array('label'=>'Опубликовать мероприятие',
        'url'=>array('/admin/projectEvent/setStatus', 'id'=>$model->id, 'status' => 'active'));
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{
    $this->menu[] = array('label'=>'Завершить мероприятие',
        'url'=>array('/admin/projectEvent/setStatus', 'id'=>$model->id, 'status' => 'finished'));
}

$this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
));

$dateFormatter = new CDateFormatter('ru');
?>

<h1><?php echo $model->name; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
        /*array(
            'label' => ProjectsModule::t('project'),
            'value' => $model->project->name,
        ),*/
		'name',
		'description:html',
		array(
            'label' => ProjectsModule::t('timestart'),
            'value' => $dateFormatter->format('dd/MM/yyyy HH:mm', $model->timestart), 
        ),
		array(
            'label' => ProjectsModule::t('timeend'),
            'value' => $dateFormatter->format('dd/MM/yyyy HH:mm', $model->timeend), 
        ),
		//'addressid',
        array(
            'label' => ProjectsModule::t('status'),
            'value' => $model->statustext,
        ),
	),
)); 
?>

<h2>Вакансии</h2>

<?php 

$elements = array();
foreach ( $model->vacancies as $vacancy )
{
    $url = Yii::app()->createUrl('/admin/eventVacancy/view', array('id' => $vacancy->id));
    $element = array();
    $element['id'] = $vacancy->id;
    $element['name'] = CHtml::link($vacancy->name, $url);
    $element['status'] = $vacancy->statustext;
    $element['limit'] = $vacancy->limit;
    $elements[] = $element;
}

$arrayProvider = new CArrayDataProvider($elements);
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'         => 'striped bordered condensed',
    'dataProvider' => $arrayProvider,
    'template'=>"{items}{pager}",
    'columns'=>array(
        array('name'=>'name', 'header'=>ProjectsModule::t('name'), 'type' => 'html'),
        array('name'=>'limit', 'header'=>ProjectsModule::t('vacancy_limit')),
        array('name'=>'status', 'header'=>ProjectsModule::t('status')),
    ),
));

?>
