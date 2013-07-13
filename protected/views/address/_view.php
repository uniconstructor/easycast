<?php
/* @var $this AddressController */
/* @var $data Address */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('objecttype')); ?>:</b>
	<?php echo CHtml::encode($data->objecttype); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('objectid')); ?>:</b>
	<?php echo CHtml::encode($data->objectid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('postalcode')); ?>:</b>
	<?php echo CHtml::encode($data->postalcode); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('country')); ?>:</b>
	<?php echo CHtml::encode($data->country); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('region')); ?>:</b>
	<?php echo CHtml::encode($data->region); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('city')); ?>:</b>
	<?php echo CHtml::encode($data->city); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('streettype')); ?>:</b>
	<?php echo CHtml::encode($data->streettype); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('streetname')); ?>:</b>
	<?php echo CHtml::encode($data->streetname); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('number')); ?>:</b>
	<?php echo CHtml::encode($data->number); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('housing')); ?>:</b>
	<?php echo CHtml::encode($data->housing); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('gate')); ?>:</b>
	<?php echo CHtml::encode($data->gate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('floor')); ?>:</b>
	<?php echo CHtml::encode($data->floor); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('apartment')); ?>:</b>
	<?php echo CHtml::encode($data->apartment); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timecreated')); ?>:</b>
	<?php echo CHtml::encode($data->timecreated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timemodified')); ?>:</b>
	<?php echo CHtml::encode($data->timemodified); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('latitude')); ?>:</b>
	<?php echo CHtml::encode($data->latitude); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('longitude')); ?>:</b>
	<?php echo CHtml::encode($data->longitude); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('encrypted')); ?>:</b>
	<?php echo CHtml::encode($data->encrypted); ?>
	<br />

	*/ ?>

</div>