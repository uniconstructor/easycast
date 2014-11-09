<?php
/**
 * Главная страница для заказчика
 */
/* @var $this SiteController */

?>
<div class="page">
    <?php
    // главное меню заказчика 
    $this->widget('ext.ECMarkup.ECResponsiveMenu.ECResponsiveMenu');
    ?>
</div>
<?php 
// слайдер с картинками (временно убран)
//$this->widget('ext.ECMarkup.ECResponsiveSlider.ECResponsiveSlider');
?>
<div class="page">
    <div class="title-page">
        <h4 class="title">Наши услуги</h4>
    </div>
    <?php 
    // все сервисы, плиткой
    $this->widget('ext.ECMarkup.EServiceList.EServiceList');
    ?>
</div>
<div class="page-alternate">
    <div class="title-page">
        <h4 class="title">Отзывы</h4>
    </div>
    <div class="container">
        <?php
        // отзывы заказчиков
        $this->widget('ext.ECMarkup.ECTestimonials.ECTestimonials');
        ?>
    </div>
</div>