<?php
/**
 * Переключатель заказчик/участник
 */
/* @var $this ECHeader */
?>
<div class="ec-switch">
    <span class="ec-left">Участникам</span>
    <a class="ec-switch_href" id="switch"
            href="#">
        <img id="ec-switch_but" src="<?= Yii::app()->createAbsoluteUrl('//');?>/images/switch_but.png"
            style="margin-left:<?= $this->defaultSwitchMargin ?>px;">
    </a>
    <span class="ec-right">Заказчикам</span>
</div>