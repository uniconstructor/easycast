<?php
/**
 * Отображение всех действий для заявки
 */
/* @var $this MemberActions */
?>
<div>
    <div id="<?= $this->containerId; ?>">
    <?php 
    foreach ( $this->buttons as $type )
    {// отображаем все доступные кнопки
        echo $this->getButton($type).'&nbsp;';
    }
    ?>
    </div>
    <div id="<?= $this->messageId; ?>" class="<?= $this->messageClass; ?>" style="<?= $this->messageStyle; ?>">
    <?= $this->message; ?>
    </div>
</div>