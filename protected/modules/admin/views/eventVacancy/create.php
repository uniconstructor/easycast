<?php
/**
 * Страница создания роли
 */
/* @var $this  EventVacancyController */
/* @var $model EventVacancy */

$this->breadcrumbs = array(
    'Администрирование'   => array('/admin'),
    'Проекты'             => array('/admin/project'),
    $event->project->name => array('/admin/project/view', 'id' => $event->project->id),
    $event->name          => array('/admin/projectEvent/view','id' => $event->id),
	'Добавить роль',
);

$this->menu = array(
	array(
	    'label' => 'Вернуться к событию',
	    'url'   => array('/admin/projectEvent/view', 'id' => $event->id)
    ),
);
?>
<h1>Добавить роль</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'event' => $event)); ?>
<?php 
// @todo событие создается в 3 шага
/*$this->widget('admin.extensions.VacancyWizard.VacancyWizard', array(
    'vacancy' => $model,
));*/

