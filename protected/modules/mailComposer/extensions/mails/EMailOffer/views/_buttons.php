<?php
/**
 * Кнопки заказа и расчета стоимости
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640"  border="0" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<td width="151" height="206" rowspan="3">&nbsp;</td>
		<td width="339" height="68">
			<a href="<?= $this->getOrderPageUrl(); ?>">
                <img src="<?= $this->getImageUrl('/images/offer/zakaz.png'); ?>">
			</a>
		</td>
		<td width="151" rowspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="339" height="56">
			<a href="<?= $this->getCalculationPageUrl(); ?>">
                <img src="<?= $this->getImageUrl('/images/offer/price.png'); ?>">
			</a>
		</td>
	</tr>
	<tr>
		<td width="339" height="82">&nbsp;</td>
	</tr>
</tbody>
</table>