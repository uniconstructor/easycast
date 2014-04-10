<?php
/**
 * Шапка страницы (для темы Maximal)
 * @todo сделать версию лого высотой 75px
 */
/* @var $this ECResponsiveHeader */
?>
<!-- Header -->
<header>
    <div class="main-nav text-center">
        <!--div class="pull-left main-contacts" style="text-align:left;">
            +7(495)227-5-226 (Заказчикам)<br>
            +7(968)590-88-00 (Пользователям)<br>
        </div-->
        <a href="<?= Yii::app()->createAbsoluteUrl('//'); ?>" title="easyCast">
        <?= CHtml::image(Yii::app()->createAbsoluteUrl('//images/logo.png'), 'easyCast', array('style' => 'max-height:60px;')); ?>
        </a>
    </div>
</header>
<!-- End Header -->