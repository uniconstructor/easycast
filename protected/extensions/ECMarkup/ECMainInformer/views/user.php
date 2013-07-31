<?php
/**
 * Отображение информации для участника - новые сообщения и приглашения
 */
?>
<div class="ec_user_info">
    <h4>Заявки и приглашения</h4>
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
            Предстоящие съемки:&nbsp;<?= $events; ?>
        <!--/a-->
        <? } ?>
    </div>
</div>