<?php
/**
 * Отображение полной информации о пользователе при AJAX-запросе со страницы "Мой выбор"
 */

?>
<div class="row">
    <div class="span12"><hr></div>
    <div class="span5">
        <?php 
        // Список фото и видео
        $this->widget('questionary.extensions.widgets.QUserMedia.QUserMedia', array(
            'questionary' => $questionary,
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