<div class="well view">

	<b>
    <?php 
        // ссылка на просмотр
        $url = Yii::app()->createUrl('/admin/fastOrder/view', array('id' => $data->id));
        echo CHtml::link('Заказ №'.$data->id, $url);
    ?>
    </b>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timecreated')); ?>:</b>
	<?php echo CHtml::encode(date('Y-m-d H:i',$data->timecreated)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->statustext); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('comment')); ?>:</b>
	<?php echo CHtml::encode($data->comment); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ourcomment')); ?>:</b>
	<?php echo CHtml::encode($data->ourcomment); ?>
	<br />

	<?php // ответственный
	if ( $data->solver )
	{
	    echo '<b>'.CHtml::encode($data->getAttributeLabel('solverid')).':</b>';
	    	    echo CHtml::encode($data->solver->fullname);
	}
	?>
	
	<br />
	<?php 
	// Кнопка "Взять"
	$pendingButton = '';
	if ( $data->status == 'active' )
	{
    	$pendingUrl = Yii::app()->createUrl('/admin/fastOrder/setStatus', 
                            array('id' => $data->id, 'status' => 'pending'));
    	$pendingButton = CHtml::link('Взять себе', $pendingUrl, array('class' => 'btn btn-success'));
	}
	echo $pendingButton;
	
	// Кнопка "Пометить обработанным"
	$closedButton = '';
	if ( $data->status == 'pending' )
	{
	    $closedUrl = Yii::app()->createUrl('/admin/fastOrder/setStatus',
	                        array('id' => $data->id, 'status' => 'closed'));
	    $closedButton = CHtml::link('Пометить обработанным', $closedUrl, array('class' => 'btn btn-success'));
	}
	echo $closedButton;
	?>
	<br>    

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('customerid')); ?>:</b>
	<?php echo CHtml::encode($data->customerid); ?>
	<br />
	
	

	*/ ?>

</div>