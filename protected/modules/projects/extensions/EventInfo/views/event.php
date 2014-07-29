<?php
/**
 * Верстка для отображения одного блока с мероприятием или приглашением
 */
/* @var $this EventInfo */
/* @var $event ProjectEvent */
?>
<div class="media" style="overflow:visible;">
    <div class="media-body" style="overflow:visible;">
        <div class="row-fluid">
            <?= $logo; ?>
            <div class="<?= $contentClass; ?>">
                <p style="margin-bottom:5px;">
                    <span class="lead text-warning">
                        <?= $event->getFormattedTimeStart(); ?>
                    </span><!--?= $this->getTopJoinButton(); ?-->
                </p>
                <h4 class="media-heading"><?= $event->project->name; ?></h4>
                <?= $eventLabels; ?>
                <p><?= $shortEventInfo; ?><br>
                    <a href="<?= $eventUrl; ?>" class="pull-right" target="_blank"
                    data-toggle="tooltip" data-title="Посмотреть всю информацию (в отдельной вкладке)">Подробнее &gt;</a>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="row-fluid">
    <?php echo $this->getVacancyList(); ?>
</div>