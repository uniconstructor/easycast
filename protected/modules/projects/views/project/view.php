<?php
/**
 * Отображение одного проекта
 */
/* @var $this ProjectController */

$this->breadcrumbs=array(
    ProjectsModule::t('projects')=>array('/projects'),
    $model->name,
);

$dateFormatter = new CDateFormatter('ru');
?>

<h1>Проект "<?php echo $model->name; ?>"</h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'typetext',
		'shortdescription:html',
		array(
            'label' => ProjectsModule::t('description'),
            'type' => 'html',
            'value' => $model->description,
        ),
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
		//'leader.fullname',
		//'customerid',
		//'orderid',
		//'isfree',
		//'memberscount',
		//'statustext',
	),
)); ?>

<h2>Мероприятия проекта</h2>

<?php 

$elements = array();
foreach ( $model->userevents as $event )
{
    $url = Yii::app()->createUrl('/projects/event/view', array('id' => $event->id));
    $element = array();
    $element['id'] = $event->id;
    $element['name'] = CHtml::link($event->name, $url);
    $element['description'] = $event->description;
    $elements[] = $element;
}

$arrayProvider = new CArrayDataProvider($elements);
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'         => 'striped bordered condensed',
    'dataProvider' => $arrayProvider,
    'template'=>"{items}{pager}",
    'columns'=>array(
        array('name'=>'name', 'header'=>ProjectsModule::t('name'), 'type' => 'html'),
        array('name'=>'description', 'header'=>ProjectsModule::t('description')),
    ),
));

?>