<?php
/**
 * Вывести изображение и текст на всю ширину колонки
 */
/* @var $this EMailSegment */
?>
<layout>
<table class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>" border="0" cellpadding="0"
    cellspacing="0">
    <tbody>
        <tr>
            <td class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>">
                <?php 
                // Выводим заголовок (align=left)
                $this->render('misc/_title');
                ?>
            </td>
        </tr>
        <tr>
            <td class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>">
                <?php 
                // Выводим изображение (полная ширина)
                $this->render('misc/_image');
                ?>
            </td>
        </tr>
        <?php 
        // Добавляем отступ между абзацами (15px)
        $this->render('misc/_hspacer');
        ?>
        <tr>
            <td class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>">
            <?php 
            // Выводим параграф текста (align=left)
            $this->render('misc/_text');
            ?>
            </td>
        </tr>
        <?php 
        // Добавляем отступ между абзацами (10px)
        $this->render('misc/_hspacer');
        ?>
    </tbody>
</table>
</layout>
