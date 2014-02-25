<?php
/**
 * Таблица со списком услуг в коммерческом предложении в теле письма
 */
/* @var $this EMailOffer */
?>
<table width="640px" border="0" cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
		<td><?= $this->createServicePhoto('media_actors'); ?></td>
		<td><?= $this->createServicePhoto('actors'); ?></td>
		<td><?= $this->createServicePhoto('models'); ?></td>
		<td><?= $this->createServicePhoto('children_section'); ?></td>
		<td><?= $this->createServicePhoto('castings'); ?></td>
	</tr>
	<tr>
		<td><?= $this->createServicePhoto('mass_actors'); ?></td>
		<td><?= $this->createServicePhoto('emcees'); ?></td>
		<td><?= $this->createServicePhoto('singers'); ?></td>
		<td><?= $this->createServicePhoto('dancers'); ?></td>
		<td><?= $this->createServicePhoto('musicians'); ?></td>
	</tr>
	<tr>
		<td><?= $this->createServicePhoto('circus_actors'); ?></td>
		<td><?= $this->createServicePhoto('sportsmen'); ?></td>
		<td><?= $this->createServicePhoto('types'); ?></td>
		<td><?= $this->createServicePhoto('animals'); ?></td>
		<td><?= $this->createServicePhoto('transport'); ?></td>
	</tr>
	</tbody>
</table>