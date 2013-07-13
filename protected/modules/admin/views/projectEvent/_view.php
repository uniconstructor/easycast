<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('projectid')); ?>:</b>
	<?php echo CHtml::encode($data->projectid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timestart')); ?>:</b>
	<?php echo CHtml::encode($data->timestart); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timeend')); ?>:</b>
	<?php echo CHtml::encode($data->timeend); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timecreated')); ?>:</b>
	<?php echo CHtml::encode($data->timecreated); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('timemodified')); ?>:</b>
	<?php echo CHtml::encode($data->timemodified); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('addressid')); ?>:</b>
	<?php echo CHtml::encode($data->addressid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	*/ ?>

</div>