<?php
/**
 * Абзац текста во всю ширину письма или любая произвольная верстка той же ширины
 */
/* @var $this EMailSegment */
?>
<table class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>" 
    border="0" cellpadding="0" cellspacing="0" style="page-break-after:<?= $this->pageBreakAfter; ?>;">
    <tbody>
        <tr>
            <td class="w<?= $this->blockWidth; ?>" width="<?= $this->blockWidth; ?>">
            <?= $this->text; ?>
            </td>
        </tr>
    </tbody>
</table>