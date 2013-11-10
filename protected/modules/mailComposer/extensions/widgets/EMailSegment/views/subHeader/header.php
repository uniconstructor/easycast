<?php
/**
 * Подзаголовок письма с возможностью указать справа дополнительную информацию
 */
/* @var $this EMailSegment */
?>
<table class="w580" width="580" border="0" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
        <td class="w400" width="400" align="left">
        <?php
        // Выводим заголовок (align=left)
        if ( $this->header )
        {
            $this->render('misc/_title', array(
                'align' => 'left',
                'title' => $this->header,
            ));
        }
        ?>
        </td>
        <td class="w180" width="180" style="text-align:right;font-size:16px;color:<?= $this->headerColor; ?>">
            <?= $this->headerInfo; ?>
        </td>
    </tr>
</tbody>
</table>
<table class="w580" width="580" border="0" cellpadding="0" cellspacing="0">
<?php 
// Добавляем отступ между абзацами (580x10)
if ( $this->addHeaderRuler )
{
    $this->render('misc/_hspacer', array('height' => 1, 'style' => $this->headerRulerStyle));
}
?>
</table>