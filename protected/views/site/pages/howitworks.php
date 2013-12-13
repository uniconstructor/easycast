<?php
/**
 * Страница "как это работает"
 */
/* @var $this SiteController */


// подключаем стили этой страницы
Yii::app()->clientScript->registerCssFile(Yii::app()->createUrl('/css/lp2-css.css'));

// название страницы
$this->pageTitle = 'Как это работает';
// навигация
$this->breadcrumbs = array(
    'Как это работает',
);
?>
<div class="lp-ec-wrapper">
    <div class="lp-our_services">
        <h1>Ваши онлайн сервисы:</h1>
    </div>
    <div class="lp-p2_banner">
        <p>
            8-летний опыт и 2 года ит-разработок позволили нам запустить
            первый в России автоматизированный ресурс для предоставления
            полного спектра кастинговых услуг <span
                style="font-style: italic; color: #55b0c6;">при помощи
                нескольких кликов</span>
        </p>
    </div>
    <div class="lp-our_services">

        <ul>
            <li class="lp-uslugi_first"></li>
            <li><a href="#"> <img src="/images/howitworks/p1.png" alt="" title="" />
                    <h3>Срочный заказ</h3>
            </a></li>
            <li class="lp-uslugi_dot"></li>
            <li><a href="<?= Yii::app()->createAbsoluteUrl('/search'); ?>"> <img src="/images/howitworks/p2.png" alt="" title="" />
                    <h3 class="padding">Поиск</h3>
            </a></li>
            <li class="lp-uslugi_dot"></li>
            <li><a href="<?= Yii::app()->createAbsoluteUrl('/catalog'); ?>"> <img src="/images/howitworks/p3.png" alt="" title="" />
                    <h3>Интернет магазин</h3>
            </a></li>
            <li class="lp-uslugi_dot"></li>
            <li><a href="<?= Yii::app()->createAbsoluteUrl('/onlineCasting'); ?>"> <img src="/images/howitworks/p4.png" alt="" title="" />
                    <h3>Видео кастинг</h3>
            </a></li>
            <li class="lp-uslugi_dot"></li>
            <li><a href="<?= Yii::app()->createAbsoluteUrl('/services'); ?>"> <img src="/images/howitworks/p5.png" alt="" title="" />
                    <h3>Фото кастинг</h3>
            </a></li>
            <li class="lp-uslugi_dot"></li>
            <li><a href="<?= Yii::app()->createAbsoluteUrl('/calculation'); ?>">
                    <img src="/images/howitworks/p6.png" alt="" title="" />
                    <h3>
                        Расчет <span class="font11">стоимости</span>
                    </h3>
            </a></li>
            <li class="lp-uslugi_dot"></li>
            <li><a href="#"> <img src="/images/howitworks/p7.png" alt="" title="" />
                    <h3>
                        Фото <span class="font8">документация</span>
                    </h3>
            </a></li>
            <li class="lp-uslugi_last"></li>
        </ul>
    </div>
    <div class="lp-p2_order">
        <a href="<?= Yii::app()->createAbsoluteUrl('/tour'); ?>">Видео-тур по системе</a>
    </div>
</div>