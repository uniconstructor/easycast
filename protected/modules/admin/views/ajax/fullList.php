<?php
/**
 * Разметка страницы полного списка всех участников
 */
/* @var $this AjaxController */
/* @var $vacancy EventVacancy */

$this->pageHeader = CHtml::encode($vacancy->event->project->name);
$this->subTitle   = 'Все заявки';

$member = ProjectMember::model()->findByPk(6690);

$this->widget('admin.extensions.smart.SmartMemberInfo', array(
    'projectMember' => $member,
    'vacancy'       => $vacancy,
));
?>
