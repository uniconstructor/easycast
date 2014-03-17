<?php
/**
 * Отображение одного отзыва на в слайдере новых событий
 */
/* @var $this ECNewEvents */
?>
<div style="display:inline-block;">
    <?php
    $this->widget('projects.extensions.EventInfo.EventInfo', array(
        'event'    => $data,
        'userMode' => 'user',
    ));
    ?>
</div>