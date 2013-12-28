<?php
/**
 * Подвал страницы
 */
/* @var $this ECFooter */
?>
<div id="footer" class="content">
    <div class="row">
        <div class="span12">
            <hr noshade size="2" style="border-color: white;">
        </div>
    </div>
    <!-- Нижнее меню для участника -->
    <div class="row-fluid show-grid">
        <div class="span12">
    		<ul class="ec-footer-menu">
    			<li><a href="<?= Yii::app()->createUrl('/questionary/questionary/view'); ?>"><span>Моя страница</span></a></li>
    			<li>|</li>
    			<li><a href="<?= Yii::app()->createUrl('/calendar'); ?>"><span>Календарь</span></a></li>
    			<li>|</li>
    			<li><a href="<?= Yii::app()->createUrl('/projects'); ?>"><span>Наши проекты</span></a></li>
    			<li>|</li>
    			<!--li><a href="#"><span>Наши события</span></a></li>
    			<li>|</li-->
    			<li><a href="<?= Yii::app()->createUrl('/onlineCasting'); ?>"><span>Онлайн - кастинг</span></a></li>
    			<li>|</li>
    			<!--li><a href="#"><span>Онлайн - консультант</span></a></li>
    			<li>|</li-->
    			<li><a href="<?= Yii::app()->createUrl('/site/page/view/about'); ?>"><span>О нас</span></a></li>
    			<li>|</li>
    			<li><a href="<?= Yii::app()->createUrl('/forum'); ?>"><span>Форум</span></a></li>
    		</ul>
    	</div>
    </div>
    <!-- Контакты -->
    <div class="row-fluid show-grid">
        <div class="span6 offset1 ec-footer-contacts">
            <p><span>Горячая линия</span> для заказчиков 
                (24 часа):&nbsp;<span><?= Yii::app()->params['customerPhone']; ?></span> <br> sale@easycast.ru</p>
        </div>
    	<div class="span6 offset1 ec-footer-contacts">
            <p><span>Техническая поддержка</span> 
            для пользователей:&nbsp;<span><?= Yii::app()->params['userPhone']; ?></span> <br> mail@easycast.ru</p>
        </div>
    </div>
    <!-- Обратная связь -->
    <div class="row-fluid show-grid" style="margin-top: 15px;">
        <div class="span9"></div>
        <div class="span2 offset1">
            <ul class="contacts ec-contacts">
                <li><span><a style="text-transform: none; margin-left: 0;" href="/site/contact">Обратная связь</a></span></li>
            </ul>
        </div>
    </div>
    <!-- Копирайт поднял выше при помощи style, чтобы было как в макете -->
    <div class="row-fluid show-grid" style="margin-top: -32px; position: relative; display: inline-table;">
        <div class="span12 easycast-copyright">
            &copy; 2005-<?= date('Y'); ?>&nbsp;Кастинговое агенство &laquo;<a
                href="<?= Yii::app()->createUrl('/site/index'); ?>">EasyCast</a>&raquo;. Все
            права защищены.&nbsp;<br>
            <a href="<?= Yii::app()->createUrl('/site/page/view/license'); ?>"><small>Пользовательское
                    соглашение</small></a>
        </div>
    </div>
</div>
<!-- footer -->