<?php
/**
 * Страница создания мероприятия в админке
 */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Проекты'           => array('/admin/project'),
	$project->name      => array('/admin/project/view', 'id' => $project->id),
	'Добавить мероприятие',
);

$this->menu = array(
	array('label' => 'Вернуться в проект', 'url'=>array('/admin/project/view', 'id' => $project->id)),
);

$titleText = 'Добавить мероприятие';
$formFile  = '_form';
if ( $type == 'group' )
{
    $titleText = 'Создать группу';
    $formFile  = '_groupForm';
}
?>

<h1><?= $titleText; ?></h1>

<?php 
echo $this->renderPartial($formFile,
        array(
            'model'   => $model,
            'project' => $project,
            'groupid' => $groupid,
            //'video'=>$video,
            //'validatedVideos'=>$validatedVideos,
        )
    );
?>