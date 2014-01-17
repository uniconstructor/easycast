<?php
/**
 * Страница со списком всех событий из всех проектов
 */
/* @var $this AgendaController */

$this->breadcrumbs = array(
    'Наши события',
);
?>

<?php 
// все текущие события выводятся одним виджетом
$this->widget('projects.extensions.EventsAgenda.EventsAgenda', array(
    'displayActive' => true,
));
?>
