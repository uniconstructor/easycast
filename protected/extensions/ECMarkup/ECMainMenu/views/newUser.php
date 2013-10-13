<?php

?>
<div class="main-menu-item">
    <div class="main-menu-item-image clearfix">
        <a href="<?= $newUser->link; ?>" target="<?= $newUser->target; ?>"
            data-toggle="modal" data-target="#registration-modal"><?= $newUser->image; ?></a>
        <div class="main-menu-item-label">
            <a href="<?= $newUser->link; ?>" target="<?= $newUser->target; ?>" data-toggle="modal" 
                data-target="#registration-modal">
                <?= $newUser->label; ?>
            </a>
        </div>
    </div>
</div>