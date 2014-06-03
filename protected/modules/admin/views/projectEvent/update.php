<?php
/**
 * Редактирование события в проекте
 */
/* @var $this ProjectEventController */
/* @var $model ProjectEvent */

// навигация
$this->breadcrumbs=array(
    'Администрирование'   => array('/admin'),
    'Проекты'             => array('/admin/project'),
	$model->project->name => array('/admin/project/view', 'id' => $model->project->id),
	$model->name          => array('view','id'=>$model->id),
	'Редактировать событие',
);

// меню
$this->menu = array(
    array('label' => 'Просмотр', 'url' => array('/admin/projectEvent/view', 'id' => $model->id)),
);

$titleText = 'Редактирование событий';
$formFile  = '_form';
if ( $model->type == 'group' )
{// редактируется группа
    $titleText = 'Редактирование группы событий';
    $formFile  = '_groupForm';
}
$titleText .= ' "'.$model->name.'';

?>

<h1><?= $titleText; ?></h1>

<?php
// форма редактирования
echo $this->renderPartial($formFile, array('model' => $model, 'project' => $model->project, 'groupid' => 0));
?>