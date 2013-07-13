<?php
/**
 * Отображение одной анкеты из заказа пользователя
 * Имя, возраст, все достижения 
 */

?>

<div class="row well well-small" id="<?php echo $data['baseContainerId']; ?>">
    <div class="span2" style="vertical-align:middle;"><?php echo $data["avatar"]; ?></div>
    <div class="span8" id="<?php echo $data['shortInfoContainerId']; ?>">
        <h2><?php echo $data['fullName']; echo $data['age']; ?></h2>
        <?php echo $data['bages']; ?>
        <br><br>
        <?php echo $data['fullInfoButton']; echo $data['shortInfoButton']; ?>
    </div>
    <div class="span8" id="<?php echo $data['messageContainerId']; ?>" style="display:none;"></div>
    <div class="span1"><?php echo $data['deleteButton']; echo $data['restoreButton']; ?></div>
    
    <div id="<?php echo $data['fullInfoContainerId']; ?>"></div>
    <input type="hidden" id="<?php echo $data['fullInfoLoadedId']; ?>" value="0">
</div>