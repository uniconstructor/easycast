<?php
/**
 * Подача заявки на событие через динамическую форму анкеты
 */
/* @var $this  VacancyController */
/* @var $model QDynamicFormModel */
?>
<div class="container">
    <div class="row">
        <?php 
        // информация о событии на которое подается заявка
        // @todo заменить hardcoded-значение настройкой
        if ( $model->vacancy->id != 749 )
        {
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
    <div class="row">
        <span class='st__large' displayText=''></span>
        <span class='st_vkontakte_large' displayText='Vkontakte'></span>
        <span class='st_twitter_large' displayText='Tweet'></span>
        <span class='st_mail_ru_large' displayText='mail.ru'></span>
        <span class='st_tumblr_large' displayText='Tumblr'></span>
        <span class='st_googleplus_large' displayText='Google +'></span>
        <span class='st_livejournal_large' displayText='LiveJournal'></span>
        <span class='st_sharethis_large' displayText='ShareThis'></span>
        <span class='st_whatsapp_large' displayText='WhatsApp'></span>
        <?php
        // @todo включить после запуска топ-модели
        /*$this->widget('questionary.extensions.widgets.QUserInvites.QUserInvites', array(
            'questionary' => $model->questionary,
        ));*/
        ?>
    </div>
</div>
