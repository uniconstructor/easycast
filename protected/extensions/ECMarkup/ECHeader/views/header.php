<?php
/**
 * Шапка страницы
 * @package easycast
 */
/* @var $this ECHeader */
?>
<div class="ec-header">
	<div class="ec-top">
	   <div class="ec-top_left span3">
		<?php 
    		// Список контактов в левом верхнем углу
    		$this->printContacts();
		?>
		</div>
		<div class="ec-top_center span6">
			<div class="ec-logo">
				<a style="text-transform: none; margin-left: 0;" href="<?= Yii::app()->createAbsoluteUrl('//');?>">
				    <img src="<?= Yii::app()->createAbsoluteUrl('//');?>/images/logo.png" alt="EasyCast"/>
				</a>
			</div>
			<?php 
			// переключатель заказчик/участник
			$this->printSwitch();
			?>
		</div>
		<div class="ec-top_right span3">
		<?php 
		    // выводим информер для участника или заказчика
            $this->printInformer();
		?>
		</div>
	</div>
</div>
<!-- header -->