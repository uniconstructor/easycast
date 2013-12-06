<?php
/**
 * Блок c кнопкой "видеотур" в теле письма с коммерческим предложением
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640"  border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td width="162" height="70">
                <img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/tur_l.gif'); ?>"  style="display: inline" align="top" border="0" width="162"/>
            </td>
            <td width="315">
                <a href="#"><img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/tur_but.gif'); ?>"  style="display: inline" align="top" border="0" width="315"></a>
            </td>
            <td width="165">
                <img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/tur_r.gif'); ?>"  style="display: inline; margin-left: -1px;" align="top" border="0" width="165" />
            </td>
        </tr>
    </tbody>
</table>