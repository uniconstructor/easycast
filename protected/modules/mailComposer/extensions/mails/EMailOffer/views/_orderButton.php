<?php
/**
 * Блок с кнопкой "сделать заказ" в теле письма с коммерческим предложением
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640"  border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td width="194" height="62">
				<img src="<?= ECPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('/images/offer/zakaz_l.gif')); ?>"/>
			</td>
			<td width="247">
				<a href="<?= Yii::app()->createAbsoluteUrl('/order', array('offerid' => $this->offer->id)); ?>">
				<img src="<?= ECPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('/images/offer/zakaz_but.gif')); ?>"></a>
			</td>
			<td width="201">
				<img src="<?= ECPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl('/images/offer/zakaz_r.gif')); ?>" style="margin-left: -2px;"/>
			</td>
		</tr>
	</tbody>
</table>