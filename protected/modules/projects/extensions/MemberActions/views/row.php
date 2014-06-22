<?php
/**
 * Отображение всех действий для заявки
 */
/* @var $this MemberActions */
?>
<div class="row-fluid">
    <div class="span6">
        <div id="<?= $this->messageId; ?>" class="<?= $this->messageClass; ?>" style="<?= $this->messageStyle; ?>">
            <?= $this->message; ?>
        </div>
    </div>
    <div class="span6">
        <div class="row-fluid text-center">
            <div id="<?= $this->containerId; ?>">
            <?php 
            foreach ( $this->buttons as $type )
            {// отображаем все доступные кнопки
                echo $this->getButton($type).'&nbsp;';
            }
            ?>
            </div>
        </div>
    </div>
</div>