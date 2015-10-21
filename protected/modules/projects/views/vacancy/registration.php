<?php
/**
 * Подача заявки на событие через динамическую форму анкеты
 */
/* @var $this  VacancyController */
/* @var $model QDynamicFormModel */
/** @var $project Project */
$project = $model->vacancy->event->project;

// заголовок страницы для поисковой индексации и публикации в соцсетях
// @todo брать название из настроек роли
$this->pageTitle = "Регистрация для участия в проекте ".strip_tags($project->name);
// Описание страницы для поисковиков
// @todo брать описание из настроек роли
$vacancyDescription = strip_tags($project->shortdescription);
Yii::app()->clientScript->registerMetaTag($vacancyDescription, 'description', null, array('lang' => 'ru'));
if ( $project->isCloaked() )
{// запрет индексации поисковиками
    Yii::app()->clientScript->registerMetaTag('noindex', 'robots');
}
if ( $project->hasBanner() )
{// выводим баннер до основного содержимого, чтобы он располагался по во всю ширину страницы
    $bannerImage = CHtml::image($project->getBannerUrl(), CHtml::encode($project->name), array('style' => 'max-width:100%;'));
    echo CHtml::tag('div', array('class' => 'row-fluid text-center'), $bannerImage);
}
?>
<div class="container">
    <div class="row-fluid">
        <?php 
        if ( ! $project->hasBanner() )
        {// баннера нет - выводим стандартную информацию для подачи заявки через форму
            $this->widget('projects.extensions.ProjectInfo.ProjectInfo', array(
                'eventId'     => $model->vacancy->event->id,
                'displayTabs' => array('main'),
            ));
        }
        ?>
    </div>
    <div class="row-fluid">
        <?php 
        // виджет динамической формы: он сам выбирает нужные поля в зависимости от настроек роли
        $this->widget('questionary.extensions.widgets.QDynamicForm.QDynamicForm', array(
            'model' => $model
        ));
        ?>
    </div>
    <div class="row text-center">
        <?php
        // @todo другие возможные роли
        /*$this->widget('questionary.extensions.widgets.QUserInvites.QUserInvites', array(
            'questionary' => $model->questionary,
        ));*/
        ?>
    </div>
</div>
<div class="row-fluid text-center">
    
</div>