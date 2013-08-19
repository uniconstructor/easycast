<?php
/**
 * Большая квадратная кнопка-ссылка
 */
?>
<layout label="Action Button">
<table class="w580" width="580" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td class="w60" valign="top" width="60"></td>
            <td width="20"></td>
            <td class="w300" valign="top" style="border-radius:6px;background-color:<?= $this->color; ?>;" bgcolor="<?= $this->color; ?>" width="300">
                <p class="article-title" align="center" style="margin-top:6px;margin-bottom:6px;">
                    <strong>
                        <singleline label="">
                            <a href="<?= $this->link; ?>" target="_blank"><?= $this->caption; ?></a>
                        </singleline>
                    </strong>
                </p>
            </td>
            <td width="20"></td>
            <td class="w60" valign="top" width="60"></td>
        </tr>
    </tbody>
</table>
</layout>
