<?php
/**
 * Фрагмент главной страницы с контактами 
 */
/* @var $this ECContacts */
?>
<ul class="ec-contacts">
	<li><img src="/images/icon-number.png">Заказчикам:<span class="laber"><?= $this->customerPhone; ?></span></li>
	<li><img src="/images/icon-number.png">Пользователям:<span><?= $this->userPhone; ?></span></li>
	<!--li><span>mail@easycast.ru</span></li-->
	<li><img src="/images/icon-comment.png">
	   <a style="text-transform:none;margin-left:0;" href="<?= Yii::app()->createUrl('/site/contact'); ?>">
	       Обратная связь
       </a>
   </li>
	<?php 
	if ( in_array('social', $this->displayItems) )
	{// полоска с социальными кнопками, если нужно
	    $this->render('_social');
	}
	?>
</ul>