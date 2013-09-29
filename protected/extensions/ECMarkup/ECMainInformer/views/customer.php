<?php
/**
 * Отображение информации для заказчика - это корзина со списком приглашенных участников
 * и кнопка оформления заказа
 */

// считаем сколько участников в заказе
$count = 0;
if ( is_array($users) AND ! empty($users) )
{
    $count = count($users);
}
?>
<div class="ec_customer_info">
    <? if ( Yii::app()->user->checkAccess('Admin') AND false ) { ?>
    <div class="ec_customer_info_block" style="margin-bottom:5px;">
        <a href="<?= Yii::app()->createUrl('//admin/'); ?>">
            [Администрирование]
        </a>
    </div>
    <? } ?>
    <h4>Мой выбор</h4>
    <div class="ec_customer_info_block">
        <a href="<?= Yii::app()->createUrl('//catalog/catalog/myChoice'); ?>" target="_blank">
            Выбрано участников:&nbsp;<?= $count; ?>
        </a>
    </div>
    <? if ( $count > 0 ) { ?>
    <div class="ec_customer_info_block" style="margin-bottom:5px;">
        <a href="<?= Yii::app()->createUrl('//catalog/catalog/myChoice'); ?>" class="btn btn-primary btn-small">
            Перейти к заказу
        </a>
    </div>
    <? } ?>
</div>