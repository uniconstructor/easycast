<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b>Цель приглашения: </b>
	<?php echo CHtml::encode($data->objectid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('managerid')); ?>:</b>
	<?php echo CHtml::encode($data->managerid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timecreated')); ?>:</b>
	<?php echo CHtml::encode($data->timecreated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timeused')); ?>:</b>
	<?php echo CHtml::encode($data->timeused); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('comment')); ?>:</b>
	<?php echo CHtml::encode($data->comment); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('userid')); ?>:</b>
	<?php echo CHtml::encode($data->userid); ?>
	<br />

</div>