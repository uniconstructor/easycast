<?php
/**
 * Отображение одного мероприятия в общем списке
 */
/* @var $data ProjectEvent */
?>
<div class="span6">
    <div class="well well-small">
    <?php 
    $this->widget('projects.extensions.EventInfo.EventInfo', array(
        'event' => $data,
    ));
    ?>
    </div>
</div>