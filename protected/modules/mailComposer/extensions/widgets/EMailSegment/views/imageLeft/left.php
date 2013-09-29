<?php
/**
 * Вывести блок письма с изображением слева
 * @var EMailSegment $this
 */
?>
<layout>
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
                    align="left">
                    <tbody>
                        <tr>
                            <td>
                                <?php 
                                // Выводим изображение (w300)
                                $this->render('misc/_image');
                                ?>
                            </td>
                            <td class="w30" width="15"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="w30" height="5" width="15"></td>
                        </tr>
                    </tbody>
                </table>
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
        <tr>
            <td class="w580" height="10" width="580"></td>
        </tr>
    </tbody>
</table>
</layout>
