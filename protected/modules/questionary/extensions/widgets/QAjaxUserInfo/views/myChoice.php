<?php
/**
 * Отображение полной информации о пользователе при AJAX-запросе со страницы "Мой выбор"
 */

?>
<div class="row">
    <div class="span12"><hr></div>
    <div class="span5">
        <?php 
        // Список фотографий пользователя
        $this->widget('ext.ECMarkup.EThumbCarousel.EThumbCarousel', array(
            'previews'    => $questionary->getBootstrapPhotos('small'),
            'photos'      => $questionary->getBootstrapPhotos('medium'),
            'largePhotos' => $questionary->getBootstrapPhotos('large'),
            'id'          => 'ethumbcarousel'.$questionary->id,
            'echoScripts' => true,
            ));
        ?>
    </div>
    <div class="span7">
        <?php 
        // Выводим всю остальную информацию о пользователе
        $this->widget('application.modules.questionary.extensions.widgets.QUserInfo.QUserInfo', array(
            'questionary' => $questionary,
            ));
        ?>
    </div>
</div>