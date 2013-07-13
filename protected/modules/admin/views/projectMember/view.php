<?php
$this->breadcrumbs=array(
	'Участники проекта'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Все участники проекта','url'=>array('index')),
	array('label'=>'Все участники мероприятия','url'=>array('index')),
	array('label'=>'Добавить участника вручную','url'=>array('create')),
);
?>

<?php 

if ( $model->status == $model::STATUS_DRAFT OR $model->status == $model::STATUS_REJECTED )
{
    echo '<h1>Заявка от участника '.$model->member->fullname.' </h1>';
}else
{
    echo '<h1>Участник проекта '.$model->member->fullname.' </h1>';
}

?>


<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'memberid',
		'vacancyid',
		'timecreated',
		'timemodified',
		'managerid',
		'request',
		'responce',
		'timestart',
		'timeend',
		'status',
	),
)); ?>
