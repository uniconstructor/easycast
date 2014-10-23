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
Yii::app()->clientScript->registerMetaTag(strip_tags($model->vacancy->event->project->shortdescription), 'description', null, array('lang' => 'ru'));

// FIXME временно вставленный баннер
if ( $model->vacancy->id == 968 )
{// только для проекта "холостяк"
    $countUrl = Yii::app()->createAbsoluteUrl('//flipcountdown');
    Yii::app()->clientScript->registerScriptFile($countUrl.'/jquery.flipcountdown.js');
    Yii::app()->clientScript->registerCssFile($countUrl.'/jquery.flipcountdown.css');
}
?>
<div class="container">
    <div class="row-fluid">
        <?php 
        // информация о событии на которое подается заявка
        if ( ! $bannerUrl = $model->vacancy->event->project->getConfig('banner') )
        {
            $this->widget('projects.extensions.ProjectInfo.ProjectInfo', array(
                'eventId'     => $model->vacancy->event->id,
                'displayTabs' => array('main'),
            ));
        }else
        {// выводим баннер 
            echo CHtml::image($bannerUrl, '', array('style' => 'max-width:100%;'));
            // и собственное описание перед формой
            if ( $greeting = $model->vacancy->getConfig('customGreeting') )
            {
                echo $greeting;
            }
        }
        ?>
        <div class="row-fluid text-center">
            До окончания приема заявок осталось (дней/часов/минут/секунд)
            <div id="retroclockbox2"></div>
        </div>
        <script type="text/javascript">
        $('#retroclockbox2').flipcountdown({
            showDay        : true,
            showHour       : true,
            showMinute     : true,
            showSecond     : true,
            beforeDateTime : '10/26/2014 00:00:01',
            size : 'lg'
        });   
        </script>
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