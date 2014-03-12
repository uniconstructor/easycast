<?php
/**
 * Отображение полной информации о пользователе при AJAX-запросе со страницы "Мой выбор"
 */
/* @var $this QAjaxUserInfo */
?>
<div class="row-fluid">
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
        <div class="row-fluid" style="margin-bottom:5px">
            <?php 
            // выводим список умений и достижений участника 
            $this->widget('questionary.extensions.widgets.QUserBages.QUserBages', array(
                'bages' => $questionary->bages,
            ));
            ?>
        </div>
        <div class="row-fluid">
            <?php 
            // выводим всю остальную информацию о пользователе
            $this->widget('questionary.extensions.widgets.QUserInfo.QUserInfo', array(
                'questionary' => $questionary,
                'nameAsLink'  => true,
            ));
            ?>
        </div>
    </div>
</div>