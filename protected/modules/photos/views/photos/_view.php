<?php 
/**
 * Краткое отображение одной галереи из списка
 */


?>

<div class="view">

    <?php
        // Обложка галереи 
        echo CHtml::image($data->getAvatarUrl()); 
    ?>
    
	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timecreated')); ?>:</b>
	<?php echo date('Y-m-d H:i', $data->timecreated); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timemodified')); ?>:</b>
	<?php echo date('Y-m-d H:i', $data->timemodified); ?>
	<br />


</div>