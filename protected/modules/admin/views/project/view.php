<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
	'Проекты'=>array('/admin/project/admin'),
	$model->name,
);

$this->menu = array(
	array('label'=>'Список проектов','url'=>array('/admin/project/admin')),
	array('label'=>'Создать проект','url'=>array('/admin/project/create')),
	array('label'=>'Редактировать проект','url'=>array('/admin/project/update','id'=>$model->id)),
    array('label'=>'Добавить мероприятие','url'=>array('/admin/projectEvent/create', 'projectid'=>$model->id)),
    array('label'=>'Заявки','url'=>array('/admin/projectMember/index', 'projectid'=>$model->id, 'type' => 'applications')),
    array('label'=>'Подтвержденные участники','url'=>array('/admin/projectMember/index', 'projectid'=>$model->id, 'type' => 'members')),
);
if ( $model->status == Project::STATUS_DRAFT )
{// разрешаем удалять черновик проекта
    $this->menu[] = array('label'=>'Удалить проект','url'=>'#','linkOptions'=>
	    array(
	        'submit'  => array('/admin/project/delete','id' => $model->id),
	        'confirm' => 'Вы уверены, что хотите удалить этот проект? Все входящие в него мероприятия и роли также будут удалены.',
	        'csrf' => true),
    );
}

// @todo подтверждение перед сменой статуса
if ( in_array('active', $model->getAllowedStatuses()) )
{
    $this->menu[] = array('label'=>'Запустить проект',
        'url'=>array('/admin/project/setStatus', 'id'=>$model->id, 'status' => 'active'));
}
if ( in_array('finished', $model->getAllowedStatuses()) )
{
    $this->menu[] = array('label'=>'Завершить проект',
        'url'=>array('/admin/project/setStatus', 'id'=>$model->id, 'status' => 'finished'));
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

<h1>Проект "<?php echo $model->name; ?>"</h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'typetext',
        'shortdescription:html',
        'description:html',
        'customerdescription:html',
        array(
            'label' => ProjectsModule::t('timestart'),
            'value' => $dateFormatter->format('dd/MM/yyyy', $model->timestart), 
        ),
		array(
            'label' => ProjectsModule::t('timeend'),
            'value' => $dateFormatter->format('dd/MM/yyyy', $model->timeend), 
        ),
		array(
	        'label' => ProjectsModule::t('project_leaderid'),
	        'value' => $model->leader->fullname,
	    ),
		//'customerid',
		//'orderid',
		'isfree',
		//'memberscount',
		'statustext',
	),
)); ?>

<h2>Мероприятия проекта</h2>

<?php 

$elements = array();
foreach ( $model->events as $event )
{
    $url = Yii::app()->createUrl('/admin/projectEvent/view', array('id' => $event->id));
    $element = array();
    $element['id'] = $event->id;
    $element['name'] = CHtml::link($event->name, $url);
    $element['status'] = $event->statustext;
    $elements[] = $element;
}

$arrayProvider = new CArrayDataProvider($elements);
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'         => 'striped bordered condensed',
    'dataProvider' => $arrayProvider,
    'template'=>"{items}{pager}",
    'columns'=>array(
        array('name'=>'name', 'header'=>ProjectsModule::t('name'), 'type' => 'html'),
        array('name'=>'status', 'header'=>ProjectsModule::t('status')),
    ),
));

?>

