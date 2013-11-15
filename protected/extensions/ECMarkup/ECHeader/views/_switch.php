<?php
/**
 * Переключатель заказчик/участник
 */
/* @var $this ECHeader */
?>
<div class="switch">
    <span class="left">Участникам</span>
    <a class="switch_href" id="switch"
            href="#">
        <img id="switch_but" src="<?= Yii::app()->createAbsoluteUrl('//');?>/images/switch_but.png"
            style="margin-left:<?= $this->defaultSwitchMargin ?>px;">
    </a>
    <span class="right">Заказчикам</span>
</div>