<?php
/**
 * Блок с кнопкой "рассчет стоимости" в теле письма с коммерческим предложением
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640"  border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td width="194" height="44">
                <img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/price_l.gif'); ?>"/>
            </td>
            <td width="247">
                <a href="#"><img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/price_but.gif'); ?>"></a>
            </td>
            <td width="201">
                <img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/price_r.gif'); ?>" style="margin-left: -2px;"/>
            </td>
        </tr>
    </tbody>
</table>