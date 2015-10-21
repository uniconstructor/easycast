<?php
/**
 * Страница отображения одного проекта или мероприятия
 */

if ( $project->isCloaked() )
{// запрет индексации поисковиками
    Yii::app()->clientScript->registerMetaTag('noindex', 'robots');
}

// навигация
if ( is_object($event) )
{// просматриваем событие или вакансию - добавим уровень навигации со ссылкой на проект
    $this->breadcrumbs = array(
        ProjectsModule::t('projects') => array('/projects'),
        $project->name => array('/projects/projects/view', 'id' => $project->id),
        $event->name,
    );
}else
{// просматриваем информацию о проекте
    $this->breadcrumbs = array(
        ProjectsModule::t('projects') => array('/projects'),
        $project->name,
    );
}

// Выводим всю информацию
$this->widget('projects.extensions.ProjectInfo.ProjectInfo', array(
    'projectId' => $projectId,
    'eventId'   => $eventId,
    'vacancyId' => $vacancyId,
    'activeTab' => $activeTab,
));