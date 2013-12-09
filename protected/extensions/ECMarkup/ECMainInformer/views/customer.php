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

// отображаем кнопку входа/выхода
$this->widget('ext.ECMarkup.ECLoginWidget.ECLoginWidget');
?>
<?php if ( Yii::app()->user->checkAccess('Admin') AND false ) : ?>
<div class="ec_customer_info_block" style="margin-bottom:5px;">
    <a href="<?= Yii::app()->createUrl('//admin/'); ?>">
        [Администрирование]
    </a>
</div>
<?php endif; ?>
<?php if ( $count > 0 ) : ?>
<div class="ec-join_but">
    <a href="<?= Yii::app()->createUrl('//catalog/catalog/myChoice'); ?>" class="btn ec-btn-primary btn-lg">
        Мой выбор:&nbsp;<?= $count; ?>
    </a>
</div>
<?php endif; ?>
