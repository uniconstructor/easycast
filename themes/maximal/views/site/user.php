<?php
/**
 * Главная страница для участника
 */
/* @var $this SiteController */

?>
<div class="page-alternate">
    <div class="title-page">
        <h4 class="title">Меню</h4>
    </div>
    <?php $this->widget('ext.ECMarkup.ECResponsiveMenu.ECResponsiveMenu'); ?>
</div>
<div class="page-alternate">
    <div class="container">
        <div class="title-page">
            <h1>Текущие съемки</h1>
            <h4 class="title-description">
                Здесь перечислены все съемки и кастинги, которые планируются в ближайшее время. 
                Вы можете подать заявку на участие в любом событии, в котором для вас есть подходящие роли.<br>
                Если хотите больше узнать о каком-то проекте - нажмите на его иконку.
            </h4>
        </div>
        <?php 
        // все текущие события выводятся одним виджетом
        $this->widget('projects.extensions.EventsAgenda.EventsAgenda', array(
            'displayActive' => true,
        ));
        ?>
    </div>
</div>