<?php
/**
 * Отображение информации для участника - новые сообщения и приглашения
 */
?>
<div class="span2">
    <div class="ec_user_info">
        <h4 class="ec_informer_header">Уведомления</h4>
        <div class="ec_user_info_block">
            <!--a href="<?= Yii::app()->createUrl('//catalog/catalog/myChoice'); ?>"-->
                Мои приглашения:&nbsp;<?= $invites; ?>
            <!--/a-->
        </div>
        <div class="ec_user_info_block">
            <!--a href="<?= Yii::app()->createUrl('//catalog/catalog/myChoice'); ?>"-->
                Мои заявки:&nbsp;<?= $requests; ?>
            <!--/a-->
        </div>
        <div class="ec_user_info_block">
            <? if ( $events > 0 ) { ?>
            <!--a href="<?= Yii::app()->createUrl('//catalog/catalog/myChoice'); ?>"-->
                Мои съемки:&nbsp;<?= $events; ?>
            <!--/a-->
            <? } ?>
        </div>
    </div>
</div>