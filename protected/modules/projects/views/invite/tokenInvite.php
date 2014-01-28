<?php
/**
 * Страница подачи заявки на участие (из письма участника)
 */
/* @var $this InviteController */

// навигация
$this->breadcrumbs = array(
	'Приглашение',
);
// виджет со списком приглашений
$this->widget('application.modules.projects.extensions.TokenInvite.TokenInvite', array(
    'key'    => $key,
    'invite' => $invite
));