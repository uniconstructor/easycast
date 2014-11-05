<?php
/**
 * 
 */
/* @var $this EventsAgenda */
?>
<div class="container">
    <div class="title-page">
        <h1><?= $this->header; ?></h1>
        <h4 class="title-description">
            <?= $this->title; ?>
        </h4>
    </div>
    <?php 
    // все события одним виджетом
    $this->widget('ext.CdVerticalTimeLine.CdVerticalTimeLine', array(
        'events' => $this->events,
    ));
    ?>
</div>