<?php
/**
 * Страница подачи заявки на участие (из письма участника)
 */
/* @var $this InviteController */

// навигация
$this->breadcrumbs = array(
	'Приглашение',
);
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Приглашение</h1>
                    <!--h4 class="intro-description">Если ролей несколько - подать заявку можно на каждую.</h4-->
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <?php 
            // виджет со списком приглашений
            $this->widget('application.modules.projects.extensions.TokenInvite.TokenInvite', array(
                'key'    => $key,
                'invite' => $invite
            ));
            ?>
        </div>
    </div>
</div>