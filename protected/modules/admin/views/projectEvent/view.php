<?php

/**
 * Страница отображения события в админке
 * @var ProjectEvent $model 
 */

$pageTitle = $model->name;
if ( $model->type == 'group' )
{
    $pageTitle .= '[Группа мероприятий]';
}


$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
    'Проект "'.$model->project->name.'"'=>array('/admin/project/view', 'id' => $model->project->id),
	$model->name,
);

$this->menu=array(
	array('label'=>'Страница проекта','url'=>array('/admin/project/view', 'id' => $model->project->id)),
	array('label'=>'Создать мероприятие','url'=>array('/admin/projectEvent/create', 'projectid'=>$model->project->id)),
	array('label'=>'Редактировать','url'=>array('update','id'=>$model->id)),
	// @todo решить, можно ли удалять мероприятие
	/*array('label'=>'Удалить мероприятие','url'=>'#',
	    'linkOptions' => array(
	        'submit' => array('delete', 'id' => $model->id),
	        'confirm' =>'Вы уверены, что хотите удалить это мероприятие?',
	        'csrf' => true)),*/
    array('label'=>'Добавить вакансию','url'=>array('/admin/eventVacancy/create', 'eventid'=>$model->id)),
    array('label'=>'Заявки','url'=>array('/admin/projectMember/index', 'eventid'=>$model->id, 'type' => 'applications')),
    array('label'=>'Подтвержденные участники','url'=>array('/admin/projectMember/index', 'eventid'=>$model->id, 'type' => 'members')),
);

if ( in_array('active', $model->getAllowedStatuses()) )
{
    $this->menu[] = array('label'=>'Опубликовать мероприятие',
        'url'=>array('/admin/projectEvent/setStatus', 'id'=>$model->id, 'status' => 'active'),
        'linkOptions' => array(
            'confirm' => 'Это действие оповестит всех подходящих участников о начале съемок. Все вакансии мероприятия также будут активированы. ВНИМАНИЕ: после публикации мероприятия редактировать критерии отбора людей будет нельзя. На всякий случай проверьте все вакансии. Опубликовать мероприятие "'.$model->name.'"?',
        ),
    );
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

$eventTitle = $model->name;
$showDates  = true;
if ( $model->type == 'group' )
{// группы мероприятий не имеют продолжительности
    $showDates = false;
    $eventTitle = $model->name.' [Группа мероприятий]';
}

?>
<h1><?php echo $eventTitle; ?></h1>
<?php 

// информация о самом мероприятии
$this->widget('bootstrap.widgets.TbDetailView',array(
	'data' => $model,
	'attributes' => array(
        //'addressid',
	    'name',
	    array(
	        'label' => $model->getAttributeLabel('parentid'),
	        'value' => $model->group ? CHtml::link($model->group->name, Yii::app()->createUrl("/admin/ProjectEvent/view", array("id" => $model->group->id))) : 'Без группы',
            'type'  => 'html',
	    ),
	    array(
	        'label' => $model->getAttributeLabel('type'),
	        'value' => $model->getTypeLabel(),
	    ),
        array(
            'label' => $model->getAttributeLabel('timestart'),
            'value' => Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $model->timestart),
            'visible' => $showDates,
        ),
        array(
            'label' => $model->getAttributeLabel('timeend'),
            'value' => Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $model->timeend),
            'visible' => $showDates,
        ),
		'description:html',
		'memberinfo:html',
        array(
            'label' => $model->getAttributeLabel('eta'),
            'value' => 'За '.($model->eta / 60).' мин',
        ),
        array(
            'label' => ProjectsModule::t('status'),
            'value' => $model->statustext,
        ),
        array(
            'label' => $model->getAttributeLabel('salary'),
            'value' => $model->salary.' p.',
        ),
	),
)); 

// Список вакансий
$vacanciesTitle = 'Вакансии';
if ( $model->type == 'group' )
{
    $vacanciesTitle = 'Вакансии [заполняются одновременно для всех мероприятий в группе]';
}
?>
<h2><?= $vacanciesTitle; ?></h2>
<?php 

/*$elements = array();
foreach ( $model->vacancies as $vacancy )
{
    $url = Yii::app()->createUrl("/admin/eventVacancy/view", array("id" => $data->id));
    $element = array();
    $element['id'] = $vacancy->id;
    $element['name'] = CHtml::link($vacancy->name, $url);
    $element['status'] = $vacancy->statustext;
    $element['limit'] = $vacancy->limit;
    $elements[] = $element;
}
$arrayProvider = new CArrayDataProvider($elements);
*/
$vacanciesList = new CActiveDataProvider('EventVacancy', array(
        'data' => $model->vacancies,
        'pagination' => false,
    ));
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'         => 'striped bordered condensed',
    'dataProvider' => $vacanciesList,
    'template' => "{summary}{items}",
    'columns'  => array(
        array(
            'name'   => 'name',
            'header' => ProjectsModule::t('name'),
            'type'   => 'html',
            'value'  => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/eventVacancy/view", array("id" => $data->id)));',
        ),
        array(
            'name'   => 'limit',
            'header' => ProjectsModule::t('vacancy_limit'),
            'value'  => '"(".$data->membersCount."/".$data->limit.") [Заявки: ".$data->requestsCount."]"',
        ),
        array('name'=>'status', 'header'=>ProjectsModule::t('status')),
    ),
));

// ТОЛЬКО ДЛЯ ГРУПП
// список событий группы
if ( $model->type == 'group' )
{
    echo '<h2>Мероприятия, включенные в группу</h2>';
    
    $eventsList = new CActiveDataProvider('ProjectEvent', array(
        'data' => $model->events,
        'pagination' => false,
    ));
    $this->widget('bootstrap.widgets.TbGridView', array(
        'type'         => 'striped bordered condensed',
        'dataProvider' => $eventsList,
        'template'=>"{summary}{items}",
        'columns'=>array(
            array(
                'name' => 'name',
                'header' => ProjectsModule::t('name'),
                'value' => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/ProjectEvent/view", array("id" => $data->id)));',
                'type' => 'html'),
            array(
                'name' => 'timestart',
                'header' => ProjectsModule::t('timestart'),
                'value' => 'Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $data->timestart)',
            ),
            array(
                'name' => 'timeend',
                'header' => ProjectsModule::t('timeend'),
                'value' => 'Yii::app()->getDateFormatter()->format("d MMMM yyyy, HH:mm", $data->timeend)',
            ),
            /*array(
                'name' => 'salary',
                'header' => $model->getAttributeLabel('salary'),
            ),*/
            array('name'=>'status', 'header'=>ProjectsModule::t('status')),
        ),
    ));
}
?>
