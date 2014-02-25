<?php
/**
 * Таблица со списком услуг в коммерческом предложении в теле письма
 */
/* @var $this EMailOffer */
?>
<table width="640px" border="0" cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
		<td width="128"><?= $this->createServicePhoto('media_actors'); ?></td>
		<td width="128"><?= $this->createServicePhoto('actors'); ?></td>
		<td width="128"><?= $this->createServicePhoto('models'); ?></td>
		<td width="128"><?= $this->createServicePhoto('children_section'); ?></td>
		<td width="128"><?= $this->createServicePhoto('castings'); ?></td>
	</tr>
	<tr>
		<td width="128"><?= $this->createServicePhoto('mass_actors'); ?></td>
		<td width="128"><?= $this->createServicePhoto('emcees'); ?></td>
		<td width="128"><?= $this->createServicePhoto('singers'); ?></td>
		<td width="128"><?= $this->createServicePhoto('dancers'); ?></td>
		<td width="128"><?= $this->createServicePhoto('musicians'); ?></td>
	</tr>
	<tr>
		<td width="128"><?= $this->createServicePhoto('circus_actors'); ?></td>
		<td width="128"><?= $this->createServicePhoto('sportsmen'); ?></td>
		<td width="128"><?= $this->createServicePhoto('types'); ?></td>
		<td width="128"><?= $this->createServicePhoto('animals'); ?></td>
		<td width="128"><?= $this->createServicePhoto('transport'); ?></td>
	</tr>
	</tbody>
</table>