<?php
/**
 * Просто абзац текста без картинок
 * @var EMailSegment $this
 */
?>
<layout>
<table class="w580" width="580" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td class="w580" width="580">
                <?php 
                // Выводим заголовок (align=left)
                $this->render('misc/_title', array(
                    'align' => 'left',
                    'title' => $this->header,
                ));
                ?>
                <?php 
                // Выводим параграф текста (align=left)
                $this->render('misc/_text', array(
                    'align'     => 'left',
                    'text'      => $this->text,
                    'textColor' => $this->textColor,
                ));
                ?>
            </td>
        </tr>
        <?php 
        // Добавляем отступ между абзацами (580x10)
        $this->render('misc/_hspacer', array('height' => 10));
        ?>
    </tbody>
</table>
</layout>