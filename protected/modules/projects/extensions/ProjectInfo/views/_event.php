<?php
/**
 * отображения одного мероприятия в списке при выводе всех мероприятий проекта
 */
?>
<div class="row span6">
    <h4><?= CHtml::encode($event->name); ?> <?= $event->getFormattedTimePeriod(); ?></h4>
    <p><?= $bages; ?></p>
    <p><?= $event->description; ?></p>
    <p><?= $signInButton; ?></p>
    <hr>
</div>
