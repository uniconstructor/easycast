<?php
/**
 * Вывести изображение и текст на всю ширину колонки
 */
?>
<layout label="Text with full-width image">
<table class="w580" width="580" border="0" cellpadding="0"
    cellspacing="0">
    <tbody>
        <tr>
            <td class="w580" width="580">
                <?php 
                // Выводим заголовок (align=left)
                $this->render('misc/_title');
                ?>
            </td>
        </tr>
        <tr>
            <td class="w580" width="580">
                <?php 
                // Выводим изображение (w580)
                $this->render('misc/_image');
                ?>
            </td>
        </tr>
        <?php 
        // Добавляем отступ между абзацами (580x15)
        $this->render('misc/_hspacer');
        ?>
        <tr>
            <td class="w580" width="580">
            <?php 
            // Выводим параграф текста (align=left)
            $this->render('misc/_text');
            ?>
            </td>
        </tr>
        <?php 
        // Добавляем отступ между абзацами (580x10)
        $this->render('misc/_hspacer');
        ?>
    </tbody>
</table>
</layout>
