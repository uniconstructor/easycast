<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('memberid')); ?>:</b>
	<?php echo CHtml::encode($data->memberid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('vacancyid')); ?>:</b>
	<?php echo CHtml::encode($data->vacancyid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timecreated')); ?>:</b>
	<?php echo CHtml::encode($data->timecreated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timemodified')); ?>:</b>
	<?php echo CHtml::encode($data->timemodified); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('managerid')); ?>:</b>
	<?php echo CHtml::encode($data->managerid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('request')); ?>:</b>
	<?php echo CHtml::encode($data->request); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('responce')); ?>:</b>
	<?php echo CHtml::encode($data->responce); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timestart')); ?>:</b>
	<?php echo CHtml::encode($data->timestart); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timeend')); ?>:</b>
	<?php echo CHtml::encode($data->timeend); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	*/ ?>

</div>