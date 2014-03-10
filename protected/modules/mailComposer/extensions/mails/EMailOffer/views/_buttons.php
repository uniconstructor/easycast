<?php
/**
 * Кнопки заказа и расчета стоимости
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0"  style="text-align:center;">
<tbody>
	<tr>
		<td>
			<a href="<?= $this->getOrderPageUrl(); ?>">
                <img src="<?= $this->getImageUrl('/images/offer/order-mail-button.png'); ?>" style="max-height:50px;display:inline;">
			</a>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="text-align:center;">
			<a href="<?= $this->getCalculationPageUrl(); ?>">
                <img src="<?= $this->getImageUrl('/images/offer/calculation-mail-button.png'); ?>" style="max-height:50px;display:inline;">
			</a>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</tbody>
</table>