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
        <?php 
        // все текущие события выводятся одним виджетом
        $this->widget('projects.extensions.EventsAgenda.EventsAgenda', array(
            'displayActive' => true,
        ));
        ?>
    </div>
</div>