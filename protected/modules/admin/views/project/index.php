<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
	'Проекты',
);

$this->menu=array(
	array('label'=>'Создать проект','url'=>array('//admin/project/create')),
	//array('label'=>'Manage Project','url'=>array('admin')),
);
?>

<h1>Проекты</h1>

<?php 
$elements = array();
foreach ( $dataProvider->getData() as $project )
{
    $url = Yii::app()->createUrl('/admin/project/view', array('id' => $project->id));
    $element = array();
    $element['id']   = $project->id;
    $element['typetext'] = $project->typetext;
    $element['name'] = CHtml::link($project->name, $url);
    //$element['timestart'] = $project->timestart;
    //$element['timeend'] = $project->timeend;
    $element['statustext'] = $project->statustext;
    $elements[] = $element;
}

$arrayProvider = new CArrayDataProvider($elements);
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'         => 'striped bordered condensed',
    'dataProvider' => $arrayProvider,
    'template'=>"{items}{pager}",
    'columns'=>array(
        array('name'=>'name', 'header'=>ProjectsModule::t('name'), 'type' => 'html'),
        array('name'=>'typetext', 'header'=>ProjectsModule::t('project_type')),
        array('name'=>'statustext', 'header'=>ProjectsModule::t('status')),
    ),
));


?>
