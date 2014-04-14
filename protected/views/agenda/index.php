<?php
/**
 * Страница со списком всех событий из всех проектов
 */
/* @var $this AgendaController */

$this->breadcrumbs = array(
    'Наши события',
);
?>
<div class="page-alternate">
    <div class="container">
        <div class="title-page">
            <h1>Наши события</h1>
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