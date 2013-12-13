<?php
/**
 * Страница "наши услуги"
 * @todo сделать все кнопки стандартными
 * @todo новая статическая страница - гарантии с кнопкой "гарантии качества и надежности"
 */
/* @var $this SiteController */

// название страницы
$this->pageTitle = 'Наши услуги';
// навигация
$this->breadcrumbs = array(
	'Наши услуги',
);
?>

<div class="ec-serv_page span12">
	<div class="ec-serv_block span6">
		<div class="ec-serv_img">
			<img src="/images/actors-example.jpg" alt="" />
			<div>
				<span><?= CHtml::link('Стоимость', Yii::app()->createUrl('/calculation')); ?></span>
			</div>
		</div>
		<div class="ec-serv_cont">
			<div class="ec-serv_text">
				<h2>Актеры</h2>
				<p>текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст </p>
			</div>
		</div>
	</div>

	<div class="ec-serv_block span6">
		<div class="ec-serv_img">
			<img src="/images/models-examlpe.jpg" alt="" />
			<div>
				<span><?= CHtml::link('Стоимость', Yii::app()->createUrl('/calculation')); ?></span>
			</div>
		</div>
		<div class="ec-serv_cont">
			<div class="ec-serv_text">
				<h2>Модели</h2>
				<p>текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст </p>
			</div>
		</div>
	</div>

	<div class="ec-serv_block span6">
		<div class="ec-serv_img">
			<img src="/images/ams-example.jpg" alt="" />
			<div>
				<span><?= CHtml::link('Стоимость', Yii::app()->createUrl('/calculation')); ?></span>
			</div>
		</div>
		<div class="ec-serv_cont">
			<div class="ec-serv_text">
				<h2>Актеры массовых сцен</h2>
				<p>текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст </p>
			</div>
		</div>
	</div>

	<div class="ec-serv_block span6">
		<div class="ec-serv_img">
			<img src="/images/types-example.jpg" alt="" />
			<div>
				<span><?= CHtml::link('Стоимость', Yii::app()->createUrl('/calculation')); ?></span>
			</div>
		</div>
		<div class="ec-serv_cont">
			<div class="ec-serv_text">
				<h2>Типажи</h2>
				<p>текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст </p>
			</div>
		</div>
	</div>
</div>

<div class="ec-serv_buttons span12">
	<div class="ec-serv_about span12" style="font-size: 20px;">
		<p>Главная цель нашей компании - максимально качественно выполнить поставленную вами задачу.
		Стоимость услуг будет завистеть от объема работы, формата проекта и сроков выполнения задачи.</p>
	</div>
	
	<div class="span4">
		<div class="">
			<a href="#" class="btn btn-warning" style="font-size: 18px;">Связаться с нами</a>
		</div>
	</div>
	<div class="span4">
		<div class="">
			<a href="#" class="btn btn-warning btn-large" style="font-size: 18px;">Расчитать стоимость</a>
		</div>
	</div>
	<div class="span4">
		<div class="">
			<a href="#" class="btn btn-warning " style="font-size: 18px;">
			<!--img src="images/bag.png" style="margin-top: -6px;"-->
			Перейти в корзину
			</a>
		</div>
	</div>
</div>