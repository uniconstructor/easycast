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

?>
<div class="container">
    <div class="row-fluid">
        <?php 
        // @todo исправить подгрузку баннера
        //if ( ! $bannerUrl = $model->vacancy->event->project->getConfig('banner') )
        if ( ! $bannerUrl = '' )
        {// информация о событии на которое подается заявка
            $this->widget('projects.extensions.ProjectInfo.ProjectInfo', array(
                'eventId'     => $model->vacancy->event->id,
                'displayTabs' => array('main'),
            ));
        }else
        {// выводим баннер 
            echo CHtml::image($bannerUrl, '', array('style' => 'max-width:100%;'));
            // и собственное описание перед формой
            /*if ( $greeting = $model->vacancy->getConfig('customGreeting') )
            {
                echo $greeting;
            }*/
            /*$this->widget('ext.FlipCountDown.FlipCountDown', array(
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
<div class="container text-center">
    <span class='st_vkontakte_large' displayText='Vkontakte'></span>
    <span class='st_facebook_large' displayText='Facebook'></span>
    <span class='st_twitter_large' displayText='Tweet'></span>
    <span class='st_googleplus_large' displayText='Google +'></span>
    <span class='st_whatsapp_large' displayText='WhatsApp'></span>
</div>