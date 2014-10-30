<?php
/**
 * Разметка виджета управления списками
 */
/* @var $this EasyListManager */
?>
<div class="row-fluid">
    <div class="span6" id="easy-list-manager-info-<?= $this->id; ?>">
        <?php 
        // создание/редактирование списка
        if ( $this->easyList->isNewRecord )
        {
            $this->render('new');
        }else
        {
            $this->render('edit');
        }
        ?>
    </div>
    <div class="span6" id="easy-list-manager-items-<?= $this->id; ?>">
        <?php 
        // элементы списка
        if ( $this->easyList->isNewRecord )
        {
            $this->render('items');
        }
        ?>
    </div>
</div>