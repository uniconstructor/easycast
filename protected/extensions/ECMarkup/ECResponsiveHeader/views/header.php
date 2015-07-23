<?php
/**
 * Шапка страницы (для темы Maximal)
 * @todo сделать версию лого высотой 83px
 */
/* @var $this ECResponsiveHeader */
?>
<!-- Header -->
<header class="row-fluid">
    <div class="main-nav row-fluid">
        <div class="span3 text-center">
            <div class="main-contacts" style="text-align:left;">
                &nbsp;<i class="icon-phone"></i>&nbsp;&nbsp;<?= Yii::app()->params['customerPhone']; ?> (Заказчикам)<br>
                &nbsp;<i class="icon-phone"></i>&nbsp;+7(495)227-5-226 (Пользователям)<br>
                &nbsp;<i class="icon-envelope"></i>&nbsp;mail@easycast.ru
            </div>
        </div>
        <div class="span6 text-center">
            <a href="<?= Yii::app()->createAbsoluteUrl('//'); ?>" title="easyCast">
                <?= CHtml::image(Yii::app()->createAbsoluteUrl('//images/logo.png'), 'easyCast', array('style' => 'max-height:83px;')); ?>
            </a>
        </div>
        <div class="span3 text-right">
            <div class="ec-user-informer">
                <?= $this->infoBlockContent; ?>
            </div>
        </div>
    </div>
</header>
<!-- End Header -->
<?php
// скрытые блоки всплывающих modal-окон
if ( Yii::app()->user->isGuest )
{// всплывающая форма регистрации (только для гостей)
    $this->widget('ext.ECMarkup.ECRegister.ECRegister');
}