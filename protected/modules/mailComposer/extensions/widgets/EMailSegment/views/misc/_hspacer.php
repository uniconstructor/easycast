<?php
/**
 * Горизонтальный разделитель, добавляет отступ между строк таблицы
 */
/* @var $this EMailSegment */
if ( ! isset($colspan) OR ! $colspan )
{
    $colspan = '';
}else
{
    $colspan = ' colspan="'.$colspan.'" ';
}
?>
<tr>
    <td class="w20" width="20" height="<?= $height; ?>"></td>
    <td style="<?= $style; ?>" <?= $colspan; ?> class="w<?= $this->blockWidth; ?>" 
        height="<?= $height; ?>" width="<?= $this->blockWidth; ?>"></td>
    <td class="w20" width="20" height="<?= $height; ?>"></td>
</tr>