<?php
/**
 * Вывести сегмент с изображением справа
 */
?>
<layout label="Text with right-aligned image">
<table class="w580" width="580" border="0" cellpadding="0"
    cellspacing="0">
    <tbody>
        <tr>
            <td class="w580" width="580">
                <?php 
                // Выводим заголовок (align=left)
                $this->render('misc/_title');
                ?>
                <table border="0" cellpadding="0" cellspacing="0"
                    align="right">
                    <tbody>
                        <tr>
                            <td class="w30" width="15"></td>
                            <td>
                                <?php 
                                // Выводим изображение (w300)
                                $this->render('misc/_image');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="w30" height="5" width="15"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
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
