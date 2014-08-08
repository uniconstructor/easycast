<?php
/**
 * Разметка подвала
 */
/* @var $this ECResponsiveFooter */
?>
<!-- Socialize -->
<div id="social-area" class="page">
    <div class="container">
        <div class="row">
            <div class="span12">
                <nav id="social">
                    <ul>
                        <li><a href="https://vk.com/easycast" title="Наша группа Вконтакте" target="_blank"><span class="icon-vk"></span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- End Socialize -->
<!-- Footer -->
<div id="footer">
    <div class="container">
        <div class="row-fluid text-center" style="text-transform: uppercase;">
			<?php $this->printFooterMenu(); ?>
        </div>
        <div class="row-fluid text-center" style="color:#fff;padding-top:60px;padding-bottom:60px;">
            <div class="span6">
                <span style="text-transform:uppercase;">Горячая линия для заказчиков (24 часа):</span>
                <?= Yii::app()->params['customerPhone']; ?>
                <br>
                order@easycast.ru
            </div>
        	<div class="span6">
        	    <span style="text-transform:uppercase;">Техническая поддержка для пользователей:</span>
                <?= Yii::app()->params['userPhone']; ?>
                <br>
                mail@easycast.ru
            </div>
        </div>
    </div>
    <!-- End Footer -->

    <p class="credits">
        &copy;2005-<?= date('Y'); ?>
        Кастинговое агенство 
        «<a href="<?= Yii::app()->createAbsoluteUrl('//'); ?>" title="Кастинговое агенство easyCast">easyCast</a>».
        Все права защищены.
        <br>
        <small><a href="<?= Yii::app()->createAbsoluteUrl('/site/page/view/license'); ?>">Пользовательское соглашение</a></small>
    </p>
    <!-- Back To Top -->
    <a id="back-to-top" href="#"><i class="icon-arrow-up"></i></a>
    <!-- End Back to Top -->
</div>