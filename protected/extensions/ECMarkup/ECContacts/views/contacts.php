<?php
/**
 * Фрагмент главной страницы с контактами 
 */
/* @var $this ECContacts */
?>
<ul class="ec-contacts">
	<li><nobr><img src="/images/icon-number.png"><span><?= $this->customerPhone; ?></span>(Заказчикам)</nobr></li>
	<li><nobr><img src="/images/icon-number.png"><span><?= $this->userPhone; ?></span>(Пользователям)</nobr></li>
	<li><nobr><img src="/images/mailme.png">&nbsp;mail@easycast.ru</nobr></li>
	<li><img src="/images/icon-comment.png">
	   <a style="text-transform:none;text-decoration:underline;" href="<?= Yii::app()->createUrl('/site/contact'); ?>">
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