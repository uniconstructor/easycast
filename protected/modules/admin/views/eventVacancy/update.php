<?php
/**
 * Страница редактирования роли
 */
/* @var $this EventVacancyController */
/* @var $model EventVacancy */

$this->breadcrumbs = array(
    'Администрирование'          => array('/admin'),
    'Проекты'                    => array('/admin/project'),
    $model->event->project->name => array('/admin/project/view', 'id' => $model->event->project->id),
    $model->event->name          => array('/admin/projectEvent/view','id' => $model->event->id),
	$model->name                 => array('view', 'id' => $model->id),
	'Редактирование',
);

$this->menu = array(
	array(
	    'label' => 'Страница события',
	    'url'   => array('/admin/projectEvent/view', 'id' => $model->event->id),
    ),
	array(
	    'label' => 'Добавить роль',
	    'url'   => array('/admin/eventVacancy/create', 'eventid' => $model->event->id),
    ),
	array(
	    'label' => 'Просмотреть роль',
	    'url'   => array('/admin/eventVacancy/view', 'id' => $model->id),
    ),
);
?>

<h1>Редактирование роли "<?php echo $model->name; ?>"</h1>

<?php 
echo $this->renderPartial('_form', array('model' => $model)); 
/*$this->widget('admin.extensions.VacancyWizard.VacancyWizard', array(
    'vacancy' => $model,
    'step'    => $step,
));*/
?>