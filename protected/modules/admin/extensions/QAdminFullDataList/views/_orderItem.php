<?php
/**
 * Отображение одной анкеты из заказа пользователя
 * Имя, возраст, все достижения 
 */
/* @var $this QAdminFullDataList */

?>

<div class="well well-small row-fluid" style="border-radius:10px;" id="<?= $data['baseContainerId']; ?>">
    <div class="row-fluid">
        <div class="span2" style="vertical-align:middle;"><?= $data["avatar"]; ?></div>
        <div class="span8" id="<?= $data['shortInfoContainerId']; ?>">
            <h3><?= $data['fullName']; ?></h3>
            <?= $data['bages']; ?>
            <br><br>
            <?= $data['fullInfoButton']; ?><?= $data['shortInfoButton']; ?>
        </div>
        <div class="span2">
            <div id="<?= $data['messageContainerId']; ?>" style="display:none;"></div>
            <div><?= $data['actionButtons']; ?></div>    
        </div>
    </div>
    <div class="row-fluid">
        <div id="<?= $data['fullInfoContainerId']; ?>"></div>
        <input type="hidden" id="<?= $data['fullInfoLoadedId']; ?>" 
            name="<?= $data['fullInfoLoadedId']; ?>_flag" value="0">
    </div>
</div>