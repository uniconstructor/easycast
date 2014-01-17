<?php
/**
 * Содержимое главного меню участника:
 * два ряда картинок с подписями
 */
/* @var $this ECMainMenu */
?>
<div class="ec-inner_wrapper">
	<ul class="ec-choose_your_side">
        <li>
            <a href="<?= $newUser->link; ?>" id="<?= $newUser->linkid; ?>" class="icon_office" <?php $newUser->modalOptions; ?>>
            <span>Моя страница</span>
            </a>
        </li>
        <li><a href="/calendar" class="icon_calendar"><span>Календарь</span></a></li>
        <li><a href="/projects" class="icon_projects"><span>Наши проекты</span></a></li>
        <li><a href="/agenda" class="icon_galery"><span>Наши события</span></a></li>
        <!--li><a href="#" class="icon_casting"><span>Онлайн - кастинг</span></a></li>
        <li><a href="#" class="icon_konsultant"><span>Онлайн - консультант</span></a></li>
        <li><a href="#" class="icon_about"><span>О нас</span></a></li-->
        <li><a href="/forum" class="icon_forum"><span>Форум</span></a></li>
	</ul>
</div>