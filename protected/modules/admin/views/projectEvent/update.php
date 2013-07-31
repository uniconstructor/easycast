<?php
/**
 * Редактирование мероприятия из админки
 */

// навигация
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
    'Проекты'=>array('/admin/project'),
	'Проект "'.$model->project->name.'"'=>array('/admin/project/view', 'id' => $model->project->id),
	$model->name=>array('view','id'=>$model->id),
	'Редактировать мероприятие',
);

// меню
$this->menu=array(
	array('label'=>'Страница проекта','url'=>array('/admin/project/view', 'id' => $model->project->id)),
	array('label'=>'Создать мероприятие','url'=>array('/admin/projectEvent/create', 'projectid'=>$model->project->id)),
	array('label'=>'Просмотр мероприятия','url'=>array('/admin/projectEvent/view','id'=>$model->id)),
	//array('label'=>'Manage ProjectEvent','url'=>array('admin')),
);

if ( $model->type == 'group' )
{// редактируется группа
?>
    <h1>Редактирование группы мероприятий "<?php echo $model->name; ?>"</h1>
<?php 
}else
{// редактируется мероприятие
?>
    <h1>Редактирование мероприятия "<?php echo $model->name; ?>"</h1>
<?php
}
// форма редактирования мероприятия
echo $this->renderPartial('_form',array('model'=>$model, 'project'=>$model->project));
?>