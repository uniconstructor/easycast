<?php
/**
 * Просто абзац текста без картинок
 */
/* @var $this EMailSegment */
?>
<layout>
<table class="w580" width="580" border="0" cellpadding="0" cellspacing="0" style="page-break-after:auto;">
    <tbody>
        <tr>
            <td class="w580" width="580">
                <?php 
                if ( $this->header )
                {// Выводим заголовок
                    $this->render('misc/_title', array(
                        'align' => $this->headerAlign,
                        'title' => $this->header,
                    ));
                }
                if ( $this->addHeaderRuler )
                {
                    $this->render('misc/_hspacer', array('height' => 5, 'style' => $this->headerRulerStyle));
                }
                if ( $this->text )
                {// Выводим абзац текста
                    $this->render('misc/_text', array(
                        'align'     => $this->textAlign,
                        'text'      => $this->text,
                        'textColor' => $this->textColor,
                    ));
                }
                ?>
            </td>
        </tr>
        <?php
        // Добавляем отступ между абзацами (580x10)
        $this->render('misc/_hspacer', array('height' => 10, 'style' => $this->textRulerStyle));
        ?>
    </tbody>
</table>
</layout>