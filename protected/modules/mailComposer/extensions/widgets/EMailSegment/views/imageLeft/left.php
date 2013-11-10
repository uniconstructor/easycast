<?php
/**
 * Вывести блок письма с изображением слева
 */
/* @var $this EMailSegment */
?>
<layout>
<table class="w580" width="580" border="0" cellpadding="0" cellspacing="0"  style="page-break-after:auto;">
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
                ?>
                <table border="0" cellpadding="0" cellspacing="0" align="left">
                    <tbody>
                        <tr>
                            <td style="vertical-align:top" valign="top">
                                <?php 
                                // Выводим изображение
                                $this->render('misc/_image', array(
                                    'link'  => $this->imageLink,
                                    'style' => $this->imageStyle,
                                ));
                                ?>
                            </td>
                            <td class="w10" width="10"></td>
                            <td>
                                <?php 
                                // Выводим параграф текста (align=left)
                                $this->render('misc/_text', array(
                                    'align'     => 'left',
                                    'text'      => $this->text,
                                    'textColor' => $this->textColor,
                                ));
                                ?>
                            </td>
                            <td class="w10" width="10"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php
        // Добавляем отступ между абзацами (580x10)
        $this->render('misc/_hspacer', array('height' => 10, 'style' => $this->textRulerStyle));
        ?>
    </tbody>
</table>
</layout>