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
    <?php
    // текущие события
    $dateCriteria         = new CDbCriteria();
    $dateCriteria->scopes = array(
        'withDate',
        // не показываем просроченные события
        'startsAfterNow',
    );
    $this->widget('projects.extensions.EventsAgenda.EventsAgenda', array(
        'displayActive' => true,
        'criteria'      => $dateCriteria,
        'header'        => 'Наши события',
        'title'         => 'Здесь перечислены все мероприятия (съемки и кастинги), которые 
            планируются в ближайшее время. Вы можете подать заявку на участие в 
            любом проекте в котором сейчас идет набор.',
    ));
    
    // все события без определенной даты выводятся в самом конце
    $noDateCriteria         = new CDbCriteria();
    $noDateCriteria->scopes = array(
        'withoutDate',
        // если событие без конкретной даты - то для отображения в календаре 
        // в нем должна быть хотя бы 1 роль на которую идет набор 
        'hasActiveVacancies',
    );
    $this->widget('projects.extensions.EventsAgenda.EventsAgenda', array(
        'displayActive'   => true,
        'displayFinished' => false,
        'criteria'        => $noDateCriteria,
        'header'          => 'Дата уточняется',
        'title'           => 'Набор на эти проекты уже открыт, но точная дата съемок пока неизвестна.',
    ));
    ?>
</div>