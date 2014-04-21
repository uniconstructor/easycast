<?php
/**
 * Отображение списка заявок или участников
 */

$breadcrumbs = array();
if ( $type == 'applications' )
{
    $titleString = 'Заявки';
}else
{
    $titleString = 'Участники';
}
if ( $projectid )
{
    $project     = Project::model()->findByPk($projectid);
    $breadcrumbs = array(
        'Администрирование' => array('/admin'),
        'Проекты'           => array('/admin/project'),
        $project->name      => array('/admin/project/view','id'=>$project->id),
    );
    $viewMode = 'project';
    $objectid = $projectid;
}
if ( $eventid )
{
    $event       = ProjectEvent::model()->findByPk($eventid);
    $breadcrumbs = array(
        'Администрирование'   => array('/admin'),
        'Проекты'             => array('/admin/project'),
        $event->project->name => array('/admin/project/view', 'id' => $event->project->id),
        $event->name          => array('/admin/projectEvent/view','id' => $event->id),
    );
    $viewMode = 'event';
    $objectid = $eventid;
}
if ( $vacancyid )
{
    $vacancy     = EventVacancy::model()->findByPk($vacancyid);
    $breadcrumbs = array(
        'Администрирование'            => array('/admin'),
        'Проекты'                      => array('/admin/project'),
        $vacancy->event->project->name => array('/admin/project/view', 'id' => $vacancy->event->project->id),
        $vacancy->event->name => array('/admin/projectEvent/view','id' => $vacancy->event->id),
        $vacancy->name => array('/admin/eventVacancy/view', 'id' => $vacancy->id),
    );
    $viewMode = 'vacancy';
    $objectid = $vacancyid;
}
$breadcrumbs[] = $titleString;
// составляем верхнее меню навигации
$this->breadcrumbs=$breadcrumbs;

?>

<h1><?= $titleString ?></h1>

<?php
// отображаем список участников события
$this->widget('admin.extensions.ProjectMembers.ProjectMembers',array(
	'objectType'      => $viewMode,
	'objectId'        => $objectid,
    'displayType'     => $type,
    'displayFullInfo' => true,
)); 
