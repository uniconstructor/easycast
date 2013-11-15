<?php
/**
 * Фрагмент главной страницы с контактами 
 */
/* @var $this ECContacts */
?>
<ul class="contacts">
	<li><span><?= $this->phone; ?></span></li>
	<li><span>mail@easycast.ru</span></li>
	<li><span><a style="text-transform:none;margin-left:0;" href="<?= Yii::app()->createUrl('/site/contact'); ?>">Обратная связь</a></span></li>
	<?php 
	if ( in_array('social', $this->displayItems) )
	{
	    $this->render('_social');
	}
	?>
</ul>