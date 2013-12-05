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
    <td style="<?= $style; ?>" <?= $colspan; ?> class="w580" height="<?= $height; ?>" width="580"></td>
    <td class="w20" width="20" height="<?= $height; ?>"></td>
</tr>