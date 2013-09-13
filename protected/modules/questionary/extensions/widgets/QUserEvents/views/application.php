<?php
/**
 * отображение одной заявки
 * @var $vacancy EventVacancy
 */
?>
<div class="media">
    <a class="pull-left" href="<?= $eventUrl; ?>" target="_blank">
        <img class="media-object" data-src="<?= $projectLogo; ?>" src="<?= $projectLogo; ?>" style="height:100px;width:100px;">
    </a>
    <div class="media-body">
        <h4 class="media-heading">
            <a href="<?= $eventUrl; ?>" target="_blank"><?= $vacancy->event->name; ?></a>
        </h4>
        <p><b><?= $vacancy->event->getFormattedTimeStart(); ?></b></p>
        <?= $vacancy->event->description; ?>
        <p><b>Место встречи:</b></p>
        <?= $vacancy->event->meetingplace; ?>
        <p><b>Дополнительная информация:</b></p>
        <?= $vacancy->event->memberinfo; ?>
        <p><b>Роль: <?= $vacancy->name; ?></b></p>
        <?= $vacancy->description; ?>
        <?= $actions; ?>
    </div>
</div>