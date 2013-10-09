<?php
/**
 * Страница подачи заявки на участие (из письма участника)
 */

$this->breadcrumbs=array();

$this->widget('application.modules.projects.extensions.TokenInvite.TokenInvite', array(
    'key'    => $key,
    'invite' => $invite
));