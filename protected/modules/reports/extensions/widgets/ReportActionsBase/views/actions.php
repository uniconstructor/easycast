<?php
/**
 * Форма с действиями для заказа
 */
/* @var $this ReportActionsBase */
/* @var $form TbActiveForm */
?>
<div class="row-fluid">
    <div class="span4">
        <?php $this->displaySaveAction(); ?>
    </div>
    <div class="span4">
        <?php $this->displaySendMailAction(); ?>
    </div>
    <div class="span4">&nbsp;</div>
</div>