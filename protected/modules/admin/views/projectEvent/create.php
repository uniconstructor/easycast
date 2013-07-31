<?php
/**
 * Страница создания мероприятия в админке
 */

$this->breadcrumbs = array(
    'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
	$project->name=>array('/admin/project/view', 'id' => $project->id),
	'Добавить мероприятие',
);

$this->menu = array(
	array('label'=>'Страница проекта','url'=>array('/admin/project/view', 'id' => $project->id)),
	//array('label'=>'Manage ProjectEvent','url'=>array('admin')),
);
?>

<h1>Добавить мероприятие</h1>

<?php 
echo $this->renderPartial('_form',
        array(
            'model'   => $model,
            'project' => $project,
            //'video'=>$video,
            //'validatedVideos'=>$validatedVideos,
        )
    );
?>