<?php
/**
 * Вывод одного элемента главного меню: картинка + подпись
 */
?>
<td style="border: 0px;">
<div class="main-menu-item">
    <div class="main-menu-item-image clearfix">
        <a href="<?= $item->link; ?>" target="<?= $item->target; ?>"><?= $item->image; ?></a>
        <div class="main-menu-item-label">
            <?php if ( $item->visible ) { ?>
            <a href="<?= $item->link; ?>" id="<?=$item->linkid;?>" target="<?= $item->target; ?>">
                <?php } ?>
                <?= $item->label; ?>
            <?php if ( $item->visible ) { ?>
            </a>
            <?php } ?>
        </div>
    </div>
</div>
</td>