<?php
/**
 * Краткая информация об участнике
 */
/* @var $this MpMemberData */
?>
<div class="row-fluid">
    <div class="span6">
        <?php
        // Фото и видео участника
        $this->widget('questionary.extensions.widgets.QUserMedia.QUserMedia', array(
            'questionary' => $this->questionary,
        ));
        ?>
    </div>
    <div class="span6">
        <h2 class="text-center"><?= $this->questionary->fullname; ?>, <?= $this->questionary->age; ?></h2>
        <?php 
        // краткая информация
        $this->widget('bootstrap.widgets.TbDetailView', array(
            'data'       => $this->getSummaryData(),
            'attributes' => $this->getSummaryAttributes(),
        ));
        ?>
        <?php
        // блок со статусами
        $this->render('_statuses');
        ?>
    </div>
</div>