<?php
/**
 * Страница с приветствием и общей информацией о компании
 */
/* @var $this SaleController */
?>
<div style="padding:20px;border-radius:25px;background-color:rgba(255,255,255,0.5)">
    <div class="row-fluid">
        <div class="span3 text-center">
            <?= CHtml::image(Yii::app()->createAbsoluteUrl('/images/offer/icon-thumbs-up.png'), '', array('style' => 'height:42px;width:42px;')); ?>
            <h2 class="lp-about-header">10 лет опыта</h2>
            <p>Кастинговое агентство easyCast успешно работает с 2004 года.</p>
        </div>
        <div class="span3 text-center">
            <?= CHtml::image(Yii::app()->createAbsoluteUrl('/images/offer/icon-star3.png'), '', array('style' => 'height:42px;width:42px;')); ?>
            <h2 class="lp-about-header">Подбираем любых артистов</h2>
            <p>Актеры всех категорий, модели, артисты циркового жанра, всевозможные типажи, групповка и массовка.</p>
        </div>
        <div class="span3 text-center">
            <?= CHtml::image(Yii::app()->createAbsoluteUrl('/images/offer/icon-film.png'), '', array('style' => 'height:42px;width:42px;')); ?>
            <h2 class="lp-about-header">Для любых съемок</h2>
            <p>Обслуживаем фильмы, рекламу, сериалы, телепроекты, клипы и другие съемки.</p>
        </div>
        <div class="span3 text-center">
            <?= CHtml::image(Yii::app()->createAbsoluteUrl('/images/offer/icon-mobile2.png'), '', array('style' => 'height:42px;width:42px;')); ?>
            <h2 class="lp-about-header">Используем современные технологии</h2>
            <p>Мы создали мощнейшие инструменты кастинга и автоматизировали 80% своей работы.</p>
        </div>
    </div>
    <div class="row-fluid text-center banner" style="margin-top:50px;">
        <p class="slogan">Все сложное с нами легко!</p>
    </div>
</div>