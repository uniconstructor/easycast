<?php
/**
 * Шапка страницы
 * @package easycast
 */
/* @var $this ECHeader */
?>
<div class="header">
	<div class="top">
	   <div class="top_left">
		<?php 
    		// Список контактов в левом верхнем углу
    		$this->printContacts();
		?>
		</div>
		<div class="top_center">
			<div class="logo">
				<a href="<?= Yii::app()->createAbsoluteUrl('//');?>">
				    <img src="<?= Yii::app()->createAbsoluteUrl('//');?>/images/logo.png" alt="EasyCast"/>
				</a>
			</div>
			<?php 
			// переключатель заказчик/участник
			$this->printSwitch();
			?>
		</div>
		<div class="top_right">
		<?php 
		    // выводим информер для участника или заказчика
            $this->printInformer();
		?>
		</div>
	</div>
</div>
<!-- header -->