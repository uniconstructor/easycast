<?php
/* @var $this EventController */
/* @var $model ProjectEvent */
// @todo языковые строки

$this->breadcrumbs=array(
    ProjectsModule::t('projects')=>array('/projects'),
    $model->project->name=>array('/projects/project/view', 'id' => $model->project->id),
    $model->name,
);

// Сообщение о том что заявка принята
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
	),
)); 
?>

<h2>Вакансии</h2>

<?php 

$elements = array();
foreach ( $model->activevacancies as $vacancy )
{
    $addApplicationButton = '';
    if ( $vacancy->isAvailableForUser() )
    {// показываем кнопку подачи заявки только тем пользователям которые подходят условиям вакансии
        $applicationUrl = Yii::app()->createUrl('/projects/event/addApplication', array('vacancyid' => $vacancy->id));
        $addApplicationButton = CHtml::link('Подать заявку', $applicationUrl, array('class' => 'btn btn-success'));
    }
    $element = array();
    $element['id'] = $vacancy->id;
    $element['name'] = $vacancy->name;
    $element['description'] = $vacancy->description;
    //$element['limit'] = $vacancy->limit;
    $element['application'] = $addApplicationButton;
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
        //array('name'=>'limit', 'header'=>ProjectsModule::t('vacancy_limit')),
        array('name'=>'application', 'header'=>'Подать заявку'/*ProjectsModule::t('vacancy_limit')*/, 'type' => 'html'),
    ),
));

?>