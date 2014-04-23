<?php
/**
 * Отображение одного приглашения
 * (inline-стили используются в этом представлении потому что оно иногда загружается по ajax,
 * и при этом невозможно подключить нужные css)
 */
/* @var $this QUserInvites */
/* @var $invite EventInvite */

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
        <div id="invite_message<?= $invite->id; ?>" class="alert alert-success text-center" style="display:none;"></div>
        <div id="invite_buttons<?= $invite->id; ?>">
            <div class="row-fluid">
                <div class="span6">
                    <?= $this->createRejectButton($invite); ?>
                </div>
                <div class="span6">
                    <?= $this->createAcceptButton($invite); ?>
                </div>
            </div>
            <div class="row-fluid" id="invite_roles<?= $invite->id; ?>" style="display:none;">
                <?php
                // список возможных ролей 
                $this->widget('projects.extensions.VacancyList.VacancyList', array(
                    'objectType'  => 'event',
                    'event'       => $invite->event,
                    'questionary' => $this->questionary,
                ));
                ?>
            </div>
        </div>
    </div>
</div>
<hr>