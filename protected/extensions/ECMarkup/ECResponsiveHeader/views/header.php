<?php
/**
 * Шапка страницы (для темы Maximal)
 * @todo сделать версию лого высотой 83px
 * @todo придумать как сделать резиновую шапку, чтобы на телефоне виджет входа не расползался
 */
/* @var $this ECResponsiveHeader */
?>
<!-- Header -->
<header>
    <div class="main-nav">
        <div class="row-fluid">
            <div class="span3 text-left">
                <!--div class="pull-left main-contacts" style="text-align:left;">
                    +7(495)227-5-226 (Заказчикам)<br>
                    +7(968)590-88-00 (Пользователям)<br>
                </div-->
            </div>
            <div class="span6 text-center">
                <a href="<?= Yii::app()->createAbsoluteUrl('//'); ?>" title="easyCast">
                    <?= CHtml::image(Yii::app()->createAbsoluteUrl('//images/logo.png'), 'easyCast', array('style' => 'max-height:83px;')); ?>
                </a>
            </div>
            <div class="span3 text-right">
                <div class="ec-user-informer">
                    <?php $this->widget('ext.ECMarkup.ECUserInformer.ECUserInformer'); ?>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- End Header -->