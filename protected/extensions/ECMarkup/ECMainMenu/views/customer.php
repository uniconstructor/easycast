<?php
/**
 * Содержимое главного меню заказчика:
 * два ряда картинок с подписями
 */
/* @var $this ECMainMenu */

$pendengItems = '';
if ( $pendingItemsCount = FastOrder::countPendingOrderUsers() )
{// если заказчик выбрал актеров для съемки - покажем их количество
    $pendengItems = ' ('.$pendingItemsCount.')';
}
?>
<div class="ec-inner_wrapper">
	<ul class="ec-choose_your_side">
		<li>
		  <a href="#" class="icon_zakaz" id="mainmenu_item_fastorder_image"
		      data-toggle="modal" data-target="#fastOrderModal">
		      <span>Срочный заказ</span>
	       </a>
        </li>
		<li><a href="/catalog/catalog/faces" class="icon_faces"><span>Наши лица</span></a></li>
		<li><a href="/search" class="icon_search"><span>Поиск</span></a></li>
		<!--li><a href="#" class="icon_services"><span>Наши услуги</span></a></li-->
		<li><a href="/projects" class="icon_projects"><span>Наши проекты</span></a></li>
		<!--li><a href="#" class="icon_how-it-work"><span>Как это работает</span></a></li-->
		<!--li><a href="/projects/casting/create" class="icon_casting"><span>Онлайн - кастинг</span></a></li-->
		<!--li><a href="#" class="icon_locations"><span>Локейшены</span></a></li>
		<li><a href="#" class="icon_about"><span>О нас</span></a></li-->
	</ul>
	<div class="our_uslugi"></div>
</div>