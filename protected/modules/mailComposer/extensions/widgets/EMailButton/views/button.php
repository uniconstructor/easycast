<?php
/**
 * Большая квадратная кнопка-ссылка которая ведет на страницу подачи заявки
 */
?>
<layout>
<table class="w580" width="580" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td class="w60" valign="top" width="60"></td>
            <td width="20"></td>
            <td class="w300" valign="top" style="border-radius:6px;background-color:<?= $this->color; ?>;" bgcolor="<?= $this->color; ?>" width="300">
                <p class="article-title" align="center" style="margin-top:6px;margin-bottom:6px;">
                    <strong>
                        <singleline>
                            <a style="color:#FFFFFF;font-size:14pt;text-decoration:none;" href="<?= $this->link; ?>" target="_blank"><?= $this->caption; ?></a>
                        </singleline>
                    </strong>
                </p>
            </td>
            <td width="20"></td>
            <td class="w60" valign="top" width="60"></td>
        </tr>
        <tr>
            <td class="w60" valign="top" width="60"></td>
            <td width="20"></td>
            <td align="center" class="w300" valign="top" width="300">
                <small style="color:#6d6d6d;font-size:8pt;"><?= $this->description; ?></small>
            </td>
            <td width="20"></td>
            <td class="w60" valign="top" width="60"></td>
        </tr>
    </tbody>
</table>
</layout>
