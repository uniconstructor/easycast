<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('galleryid')); ?>:</b>
	<?php echo CHtml::encode($data->galleryid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timestart')); ?>:</b>
	<?php echo CHtml::encode($data->timestart); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timeend')); ?>:</b>
	<?php echo CHtml::encode($data->timeend); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('timecreated')); ?>:</b>
	<?php echo CHtml::encode($data->timecreated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timemodified')); ?>:</b>
	<?php echo CHtml::encode($data->timemodified); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('leaderid')); ?>:</b>
	<?php echo CHtml::encode($data->leaderid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('customerid')); ?>:</b>
	<?php echo CHtml::encode($data->customerid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('orderid')); ?>:</b>
	<?php echo CHtml::encode($data->orderid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('isfree')); ?>:</b>
	<?php echo CHtml::encode($data->isfree); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('memberscount')); ?>:</b>
	<?php echo CHtml::encode($data->memberscount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	*/ ?>

</div>