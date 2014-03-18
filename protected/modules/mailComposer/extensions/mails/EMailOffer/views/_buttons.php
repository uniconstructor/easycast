<?php
/**
 * Кнопки заказа и расчета стоимости
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640" border="0" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
        <td style="width:50px;">&nbsp;</td>
        <td style="width:245px;">
        	<a href="<?= $this->getOrderPageUrl(); ?>">
                <img src="<?= $this->getImageUrl('/images/offer/order-mail-button.png'); ?>" style="max-height:40px;display:inline;">
        	</a>
        </td>
        <td style="width:50px;">&nbsp;</td>
        <td style="width:245px;">
        	<a href="<?= $this->getCalculationPageUrl(); ?>">
                <img src="<?= $this->getImageUrl('/images/offer/calculation-mail-button.png'); ?>" style="max-height:40px;display:inline;">
        	</a>
        </td>
        <td style="width:50px;">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</tbody>
</table>