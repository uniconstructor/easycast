<?php
/**
 * Отображение информации для участника - новые сообщения и приглашения
 */
?>
<div class="span2">
    <div class="ec_user_info">
        <img src="images/photo3.png" alt="<?= Yii::app()->getModule('user')->user()->fullname; ?>" />
	    <a class="name_user" href="/questionary/questionary/view"> <?= Yii::app()->getModule('user')->user()->fullname; ?></a>
	    <a href="/user/logout" class="exit">Выйти</a>
        <!--h4 class="ec_informer_header">Уведомления</h4>
        <div class="ec_user_info_block">
            <a href="<?= Yii::app()->createUrl('//questionary/questionary/view', array('activeTab' => 'invites')); ?>">
                Мои приглашения:&nbsp;<?= $invites; ?>
            </a>
        </div>
        <div class="ec_user_info_block">
            <a href="<?= Yii::app()->createUrl('//questionary/questionary/view', array('activeTab' => 'requests')); ?>">
                Мои заявки:&nbsp;<?= $requests; ?>
            </a>
        </div>
        <div class="ec_user_info_block">
            <? if ( $events > 0 ) { ?>
            <a href="<?= Yii::app()->createUrl('//questionary/questionary/view', array('activeTab' => 'events')); ?>">
                Мои съемки:&nbsp;<?= $events; ?>
            </a>
            <? } ?>
        </div-->
    </div>
</div>