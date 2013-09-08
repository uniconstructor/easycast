<?php
/**
 * Отображение одного приглашения
 * (inline-стили используются в этом представлении потому что оно иногда загружается по ajax,
 * и при этом невозможно подключить нужные css)
 */

// получаем лого проекта
$projectLogo = $invite->event->project->getAvatarUrl('small');
// создаем ссылку на мероприятие
$eventUrl = Yii::app()->createUrl('/projects/projects/view', array('eventid' => $invite->event->id));

?>
<div class="media">
    <a class="pull-left" href="<?= $eventUrl; ?>" target="_blank">
        <img class="media-object" data-src="<?= $projectLogo; ?>" src="<?= $projectLogo; ?>" style="height:100px;width:100px;">
    </a>
    <div class="media-body">
        <h4 class="media-heading">
            <a href="<?= $eventUrl; ?>" target="_blank"><?= $invite->event->name; ?></a>
        </h4>
        <p><b><?= $invite->event->getFormattedTimeStart(); ?></b></p>
        <?= $invite->event->description; ?>
        <div id="invite_message<?= $invite->id; ?>" class="alert alert-success" style="display:none;text-align:center;"></div>
        <div id="invite_buttons<?= $invite->id; ?>">
            <?= $this->createAcceptButton($invite); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= $this->createRejectButton($invite); ?>
        </div>
    </div>
</div>