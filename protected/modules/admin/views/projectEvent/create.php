<?php
/**
 * Страница создания мероприятия в админке
 */
/* @var $this  ProjectEventController */
/* @var $model ProjectEvent */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Проекты'           => array('/admin/project'),
	$project->name      => array('/admin/project/view', 'id' => $project->id),
	'Добавить событие',
);
$this->menu = array(
	array('label' => 'Вернуться в проект', 'url' => array('/admin/project/view', 'id' => $project->id)),
);

$titleText = 'Добавить событие';
$formFile  = '_form';
if ( $type === 'group' )
{
    $titleText = 'Создать группу событий';
    $formFile  = '_groupForm';
}
?>

<h1><?= $titleText; ?></h1>

<?php 
// форма создания события
echo $this->renderPartial($formFile, array(
    'model'   => $model,
    'project' => $project,
    'groupid' => $groupid,
));
?>