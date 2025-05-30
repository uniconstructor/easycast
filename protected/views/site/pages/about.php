<?php
/**
 * Страница "О нас"
 * @todo добавить 3 вкладки: о компании, наши отзывы, наша команда
 * @todo убрать все контакты кроме почты и facebook
 * @todo расположить людей в 2 ряда
 */
/* @var $this SiteController */

// название страницы
$this->pageTitle = 'О нас';
// навигация
$this->breadcrumbs = array(
	'О нас',
);
?>

<div class="ec-about_block span12" style="border-top:0px;">
	<div class="ec-about_img">
		<img src="/images/ngrishin.jpg" alt="" />
	</div>
	<div class="ec-about_cont">
		<div class="ec-about_text">
			<h2>Николай Гришин <span>управляющий парнер</span></h2>
			<p><span>E-mail:</span> ceo@easycast.ru </p>
			<p><img src="/images/facebook_icon.png" /><a href="http://www.facebook.com/ngrishinru" target="_blank">www.facebook.com/ngrishinru</a></p>
		</div>
	</div>
</div>

<div class="ec-about_block span12">
	<div class="ec-about_img">
		<img src="/images/ibuzaeva.jpg" alt="" />
	</div>
	<div class="ec-about_cont">
		<div class="ec-about_text">
			<h2>Ирина Бузаева <span>руководитель проектов</span></h2>
			<p><span>E-mail:</span> irina@easycast.ru</p>
			<p><img src="/images/facebook_icon.png" /><a href="http://www.facebook.com/irsen.love" target="_blank">www.facebook.com/irsen.love</a></p>
		</div>
	</div>
</div>


<div class="ec-about_block span12">
	<div class="ec-about_img">
		<img src="/images/elarsen.jpg" alt="" />
	</div>
	<div class="ec-about_cont">
		<div class="ec-about_text">
			<h2>Елизавета Ларсен <span>руководитель проектов</span></h2>
			<p><span>E-mail:</span> liza@easycast.ru</p>
			<p><img src="/images/facebook_icon.png" /><a href="http://www.facebook.com/larsen.liza" target="_blank">www.facebook.com/larsen.liza</a></p>
		</div>
	</div>
</div>