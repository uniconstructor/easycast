<?php
/**
 * Подача заявки на событие через динамическую форму анкеты
 */
/* @var $this  VacancyController */
/* @var $model QDynamicFormModel */

// заголовок страницы для поисковой индексации и публикации в соцсетях
$this->pageTitle = "Регистрация для участия в проекте ".$model->vacancy->event->project->name;
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
    <div class="row text-center">
        <!-- ShareThis widget -->
        <!--script type="text/javascript">var switchTo5x=true;</script>
        <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
        <script type="text/javascript">stLight.options({publisher: "9144efb6-c5a7-4360-9b70-24e468be66c3", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
        <span class='st__large' displayText=''></span>
        <span class='st_vkontakte_large' displayText='Vkontakte'></span>
        <span class='st_twitter_large' displayText='Tweet'></span>
        <span class='st_mail_ru_large' displayText='mail.ru'></span>
        <span class='st_tumblr_large' displayText='Tumblr'></span>
        <span class='st_googleplus_large' displayText='Google +'></span>
        <span class='st_livejournal_large' displayText='LiveJournal'></span>
        <span class='st_sharethis_large' displayText='ShareThis'></span>
        <span class='st_whatsapp_large' displayText='WhatsApp'></span-->
        <?php
        // @todo другие возможные роли (включить после тестирования)
        /*$this->widget('questionary.extensions.widgets.QUserInvites.QUserInvites', array(
            'questionary' => $model->questionary,
        ));*/
        ?>
    </div>
</div>
