<?php
/**
 * Подача заявки на событие через динамическую форму анкеты
 */
/* @var $this  VacancyController */
/* @var $model QDynamicFormModel */

// заголовок страницы для поисковой индексации и публикации в соцсетях
// @todo брать название из настроек роли
$this->pageTitle = "Регистрация для участия в проекте ".$model->vacancy->event->project->name;

// Описание страницы для поисковиков
// @todo брать описание из настроек роли
$vacancyDescription = strip_tags($model->vacancy->event->project->shortdescription);
Yii::app()->clientScript->registerMetaTag($vacancyDescription, 'description', null, array('lang' => 'ru'));

// ссылка на баннер
$bannerUrl = Yii::app()->createAbsoluteUrl('/projects/project/banner', array('id' => $model->vacancy->event->project->id));
if ( $bannerUrl )
{// выводим баннер до основного содержимого, чтобы он располагался по во всю ширину страницы
    $bannerImage = CHtml::image($bannerUrl, $model->vacancy->event->project->name, array('style' => 'max-width:100%;'));
    echo CHtml::tag('div', array('class' => 'row-fluid text-center'), $bannerImage);
}
?>
<div class="container">
    <div class="row-fluid">
        <?php 
        if ( ! $bannerUrl )
        {// баннера нет - выводим стандартную информацию для подачи заявки через форму
            $this->widget('projects.extensions.ProjectInfo.ProjectInfo', array(
                'eventId'     => $model->vacancy->event->id,
                'displayTabs' => array('main'),
            ));
        }else
        {
            // и собственное описание перед формой
            /*if ( $greeting = $model->vacancy->getConfig('customGreeting') )
            {
                echo $greeting;
                echo CHtml::tag('div', array('class' => 'well'), $bannerImage);
            }
            $this->widget('ext.FlipCountDown.FlipCountDown', array(
                'beforeUnixTime' => $model->vacancy->getConfig(''),
                'settings'       => array('size' => 'lg'),
            ));*/
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
        // @todo другие возможные роли (включить после тестирования)
        /*$this->widget('questionary.extensions.widgets.QUserInvites.QUserInvites', array(
            'questionary' => $model->questionary,
        ));*/
        ?>
    </div>
</div>
<div class="row-fluid text-center">
    
</div>