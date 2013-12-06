<?php
/**
 * Блок "разделы каталога" в теле письма с коммерческим предложением
 */
/* @var $this EMailOffer */
?>
<table class="w640" width="640"  border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td width="43" height="176">
				<img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/serv_l.png'); ?>"/>
			</td>
			<td width="134">
				<a href="<?= $this->getSectionUrl('actors'); ?>">
				<img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/serv1.png'); ?>" width="134"></a>
			</td>
			<td width="147">
				<a href="<?= $this->getSectionUrl('ams'); ?>">
				<img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/serv2.png'); ?>"  width="147" /></a>
			</td>
			<td width="144">
				<a href="<?= $this->getSectionUrl('models'); ?>">
				<img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/serv3.png'); ?>" width="144"></a>
			</td>
			<td width="133">
				<a href="<?= $this->getSectionUrl('types'); ?>">
				<img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/serv4.png'); ?>"  width="133" /></a>
			</td>
			<td width="40" height="176">
				<img src="<?= Yii::app()->createAbsoluteUrl('/images/offer/serv_r.png'); ?>" width="39" height="176" />
			</td>
		</tr>
	</tbody>
</table>