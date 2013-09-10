<?php
/**
 * Редактирование мероприятия из админки
 */

// навигация
$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Проекты' => array('/admin/project'),
	'Проект "'.$model->project->name.'"'=>array('/admin/project/view', 'id' => $model->project->id),
	$model->name=>array('view','id'=>$model->id),
	'Редактировать мероприятие',
);

// меню
$this->menu=array(
	//array('label'=>'Страница проекта','url'=>array('/admin/project/view', 'id' => $model->project->id)),
    array('label'=>'Просмотр','url'=>array('/admin/projectEvent/view','id'=>$model->id)),
	//array('label'=>'Добавить еще мероприятие','url'=>array('/admin/projectEvent/create', 'projectid'=>$model->project->id)),
	//array('label'=>'Manage ProjectEvent','url'=>array('admin')),
);

$titleText = 'Редактирование мероприятия';
$formFile  = '_form';
if ( $model->type == 'group' )
{// редактируется группа
    $titleText = 'Редактирование группы мероприятий';
    $formFile  = '_groupForm';
}
$titleText .= ' "'.$model->name.'';

?>

<h1><?= $titleText; ?></h1>

<?php
// форма редактирования
echo $this->renderPartial($formFile, array('model' => $model, 'project' => $model->project, 'groupid' => 0));
?>